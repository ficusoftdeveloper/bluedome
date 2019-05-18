from __future__ import (absolute_import, division,print_function, unicode_literals)
from builtins import *
import numpy as np
import cv2
import SimpleITK as sitk
from builtins import *
from scipy.spatial import distance
import sys
import time
import scipy
import csv
############### FUNCTIONS  ##########################
def find_crack_connected(labels,contours):
    ncontours=len(contours)
    G_dense=np.zeros((ncontours,ncontours))
    for ct_contour in range(ncontours):
        cnt = contours[ct_contour]
        rect = cv2.minAreaRect(cnt)
        box = cv2.boxPoints(rect)
        box = np.int0(box)
        mask=np.zeros(imgorig.shape,np.float)
        mask=cv2.drawContours(mask,[box],0,(1),-1)
        kern_size=int(0.03*max(labels.shape))
        kernel = np.ones((kern_size,kern_size), np.float)
        mask=cv2.dilate(mask,kernel)
        #labels_masked=labels*mask

        neigh=np.unique(labels[mask>0])
        id_neigh=[]
        for i in range(len(neigh)):
            if neigh[i] >= 0 and neigh[i] != ct_contour:
                #id_neigh.append(neigh[i])
                G_dense[ct_contour,int(neigh[i])]=1

    G_sparse = scipy.sparse.csr_matrix(G_dense )
    cc=scipy.sparse.csgraph.connected_components( G_sparse ,directed=False)
    return cc,

def measure_cracks_width(labels,contours):
    ncontours=len(contours)
    cracks_width=[]
    for ct_contour in range(ncontours):
        mask=np.zeros(labels.shape,np.ubyte)

        mask=cv2.drawContours(mask,contours,ct_contour,(1),-1)
        dist = cv2.distanceTransform(mask,cv2.DIST_L2,5)
        width=np.amax(dist)
        cracks_width.append(width)
    return cracks_width
############ MAIN ##############

#img_file='output_bin.png'
img_file=sys.argv[1]
res=1;#mm/pixel
if len(sys.argv)==6:
   #res=sys.argv[2]
   mes_out_file=sys.argv[3]
   mes_out_csv=sys.argv[4]
   mes_out_csv_sum=sys.argv[5]

print('processing '+img_file)
imgorig=cv2.imread(img_file,cv2.IMREAD_GRAYSCALE)
imgorig[np.where(imgorig>0)]=255
print(np.shape(imgorig))
start_time = time.time()

im2, contours1, hierarchy = cv2.findContours(imgorig,cv2.RETR_TREE,cv2.CHAIN_APPROX_SIMPLE)
# remove little contours
contours=[];
for ct_contour in range(len(contours1)):
        area = cv2.contourArea(contours1[ct_contour])

        if   ( area/(np.prod(np.shape(imgorig))))>0.0001:
            contours.append(contours1[ct_contour])

ncontours=len(contours)
labels=-np.ones(np.shape(imgorig))
for ct_contour in range(ncontours):
        labels=cv2.drawContours(labels,contours,ct_contour,(ct_contour),-1)

cc=find_crack_connected(labels,contours)

## extract measure of each connected component
width_contours=measure_cracks_width(labels,contours)
width_contours=width_contours*res
areas_contours=[]
length_contours=[]
for ct_contour in range(ncontours):
    area = cv2.contourArea(contours[ct_contour])*(res*res)
    areas_contours.append(area)
    rect = cv2.minAreaRect(contours[ct_contour])
    length=max(rect[1])*res
    length_contours.append(length)


## cumulate the statistics based on the crack tree
#if len(cc)>1:
#    ncracks_connected=cc[0]
#else:
#    ncracks_connected=1

#if ncracks_connected==1:
#    id_cnt=cc[0][1]
#else:
#    id_cnt=cc[1]
ncracks_connected=cc[0][0]
id_cnt=cc[0][1]
areas_cracks_connected=np.zeros((1,ncracks_connected)).flatten()
length_cracks_connected=np.zeros((1,ncracks_connected)).flatten()
width_cracks_connected=np.zeros((1,ncracks_connected)).flatten()
for ct_contour in range(ncontours):
    id=int(id_cnt[ct_contour])
    areas_cracks_connected[id]=areas_cracks_connected[id]+areas_contours[ct_contour]
    length_cracks_connected[id]=length_cracks_connected[id]+length_contours[ct_contour]
    width_cracks_connected[id]=max( width_cracks_connected[id], width_contours[ct_contour])

## write the results
font = cv2.FONT_HERSHEY_SIMPLEX
# draw bounding boxes
imgout=cv2.cvtColor(imgorig,cv2.COLOR_GRAY2BGR);
for ct_contour in range(ncontours):
    rect = cv2.minAreaRect(contours[ct_contour])
    box = cv2.boxPoints(rect)
    box = np.int0(box)
    imgout=cv2.drawContours(imgout,[box],0,(255,0,0),2)
    center=np.mean(box,0);
    #cv2.putText(imgout, str(ct_contour), (int(center[0]), int(center[1])), font, 2, (255, 0, 0), 2, cv2.LINE_AA)
    txt=str(ct_contour)+' '+'L:'+str( int(length_contours[ct_contour]))+' W:'+str( int(width_contours[ct_contour]))+' A:'+str( int(areas_contours[ct_contour]))
    cv2.putText(imgout, txt, (int(center[0])+10, int(center[1])), font, 1, ( 0, 0,255), 2, cv2.LINE_AA)

cv2.imwrite(mes_out_file,imgout);
with open(mes_out_csv, 'w') as csvfile:
    writercsv = csv.writer(csvfile)
    writercsv.writerow(['ID', 'AREA', 'LENGTH', 'WIDTH','ID_CRACK'])
    for ct_contour in range(ncontours):
        writercsv.writerow([str(ct_contour),str( int(areas_contours[ct_contour])),str( int(length_contours[ct_contour])), str( int(width_contours[ct_contour])), int(id_cnt[ct_contour])]  )

with open(mes_out_csv_sum, 'w') as csvfile2:
    writercsv2 = csv.writer(csvfile2)
    writercsv2.writerow(['ID_CRACK', 'AREA', 'LENGTH', 'WIDTH'])
    for ct in range(ncracks_connected):
        writercsv2.writerow([str(ct),str( int(areas_cracks_connected[ct])),str( int(length_cracks_connected[ct])), str( int(width_cracks_connected[ct]))]  )
