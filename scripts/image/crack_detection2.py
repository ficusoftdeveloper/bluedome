from __future__ import (absolute_import, division,print_function, unicode_literals)
from builtins import *
import numpy as np
import cv2 
import SimpleITK as sitk
from builtins import *
from scipy.spatial import distance
from scipy import stats
import sys
############### FUNCTIONS  ##########################
def imcomplement(im):
    if np.max(im)>1:
        imout=255-im
    else:
        imout=1-im
    return imout

def mat2gray(img):
    max_img=np.max(img)
    min_img=np.min(img)
    imgout=(img-min_img)/(max_img-min_img)
    return imgout

def im2double(img):
    imgout=img.astype('float32')
    imgout= mat2gray(imgout)
    return imgout

def imreconstruct(marker,mask):
    markeritk=sitk.GetImageFromArray(marker)
    maskitk=sitk.GetImageFromArray(mask)
    recfilt=sitk.ReconstructionByDilationImageFilter()
    rectoutitk=recfilt.Execute(markeritk,maskitk)
    rectout=sitk.GetArrayFromImage(rectoutitk)
    return rectout

def eigen_cov(x,y):
    mx=np.mean(x)
    my=np.mean(y)
    x=x-mx
    y=y-my
    cxx=np.var(x)
    cxy=0
    cyy=np.var(y);
    nx=len(x)
    for ct in range(nx):
        cxy=cxy+x[ct]*y[ct]; 
    cxy=cxy/nx;
    C=np.zeros((2,2))
    C[0,0]=cxx
    C[0,1]=cxy
    C[1,0]=cxy
    C[1,1]=cyy
    D,V=np.linalg.eig(C)
    return V,D

def is_inside(img,x,y):
    x=int(x)
    y=int(y)
    if(x>0 and y>0 and x<img.shape[1] and y<img.shape[0]):
        return True
    else:
        return False

def improfile(img,x,y,n):
    xm=x[0]
    x0=x[1]
    ym=y[0]
    y0=y[1]
    
    a = np.arctan((y0 - ym) / (x0 - xm))
    i=range(0,100,int(100/n))
    cx=np.squeeze(np.zeros((1,len(i))))
    cy=np.squeeze(np.zeros((1,len(i))))
    c=np.squeeze(np.zeros((1,len(i))))
    ct=0
    for t in range(0,100,int(100/30)):
            tf=t/100.0
            cx[ct] = int(xm + (x0 - xm)*tf)
            cy[ct] = int(ym + (y0 - ym)*tf)
            if(is_inside(img,cx[ct],cy[ct])):
                c[ct]=img[int(cy[ct]), int(cx[ct])]
            else:
                c[ct]=1;
            ct=ct+1
    return c,cx,cy
def filter_result3(img,bw_result,ths,thm):
    bw_result_orig=np.copy(bw_result);
    points=np.where(bw_result>0)
    points=np.reshape(points,np.shape(points))
    points=np.transpose(points)
    npoints=np.shape(points)[0]
    k=20
    step=5
    hsv=cv2.cvtColor(img,cv2.COLOR_BGR2HSV)
    sat=hsv[:,:,1]/255
    bw_result_filter=np.zeros(np.shape(bw_result))
    xc=points[:,1]
    yc=points[:,0]
    for ct in range(0,npoints,step):
        #print(ct/npoints)
        ystart=max(0,yc[ct]-k);
        xstart=max(0,xc[ct]-k);
        yend=min(np.shape(img)[0],yc[ct]+k);
        xend=min(np.shape(img)[1],xc[ct]+k);


        p=points[ct,:]
        p=np.reshape(p,(1,2))
        Dpoints=distance.cdist(p,points)
        Dpoints=np.squeeze(Dpoints)
        ipoints=np.squeeze(np.where(Dpoints<40))
        xneigh=points[ipoints,1];
        yneigh=points[ipoints,0];
        V,D=eigen_cov(xneigh,yneigh)
        vmin=V[:,0];
        if D[1]<D[0]:
            vmin=V[:,1];

        x1=xc[ct]-k*vmin[0];
        y1=yc[ct]-k*vmin[1];
    
        x2=xc[ct]+k*vmin[0];
        y2=yc[ct]+k*vmin[1];
        p,px,py=improfile(sat,np.array([x1,x2]),np.array([y1,y2]),30);
        s=np.abs(np.mean(p[0:5])-np.mean(p[len(p)-5:len(p)]));
        s=round(s*100);
        m=np.max([p[0:5],p[len(p)-5:len(p)]]);
        if(s<ths and m<thm):
            bw_result_filter[ystart:yend,xstart:xend]=bw_result_orig[ystart:yend,xstart:xend];
    return bw_result_filter
def min_openings(im,LEN,DEG_NUM):
    imo=[];
    for i in range(DEG_NUM):
         #DEG=(i)*((360/DEG_NUM)/2)
         filtername=str(i+1)+'se.txt'
         se=np.loadtxt('filters/'+filtername)
         if(i==0):
             se=np.reshape(se,(1,len(se)))
         if(i==6):
             se=np.reshape(se,(len(se),1))
         se=se.astype('uint8')
         imoi=cv2.erode(im,se)
         imoi=cv2.dilate(imoi,se)
         imo.append(imoi)
    imB=imo[0]
    for i in range(DEG_NUM-1):
        k=i+1
        imB=np.minimum(imB,imo[k])

    
    return imB

def smooth_cross_section(imV,LEN_diff,DEG_NUM):
    imV_c=imcomplement(imV)
    imd=[]
    for i in range(12):
        k=i+1
        se1=np.loadtxt('filters/'+str(k)+'linekernel1.txt')
        se2=np.loadtxt('filters/'+str(k)+'linekernel2.txt')
        if(i==0):
            se1=np.reshape(se1,(1,len(se1)))
            se2=np.reshape(se2,(len(se2),1))
        if(i==6):
            se1=np.reshape(se1,(len(se1),1))
            se2=np.reshape(se2,(1,len(se2)))

        temp=cv2.filter2D(imV_c.astype('float32'),-1,se1)
        imdi=cv2.filter2D(temp,-1,se2)
        imdi[imdi<0]=0
        imd.append(imdi)
    imDiff=imd[0]
    for i in range(11):
        k=i+1
        imDiff=np.maximum(imDiff,imd[k])
    imDiff=mat2gray(imDiff)
    return imDiff

def reconstruction_by_dilation(im,LEN,DEG_NUM):
    imo=[];
    for i in range(DEG_NUM):
         #DEG=(i)*((360/DEG_NUM)/2)
         filtername=str(i+1)+'se.txt'
         se=np.loadtxt('filters/'+filtername)
         if(i==0):
             se=np.reshape(se,(1,len(se)))
         if(i==6):
             se=np.reshape(se,(len(se),1))
         se=se.astype('uint8')
         imoi=cv2.erode(im,se)
         imoi=cv2.dilate(imoi,se)
         imo.append(imoi)
    imC=imo[0]
    for i in range(DEG_NUM-1):
        k=i+1
        imC=np.maximum(imC,imo[k])

    imC2=imreconstruct(imC,im)
    imC2=mat2gray(imC2)
    return imC2

def reconstruction_by_erosion(im,LEN,DEG_NUM):
    im_close=[];
    for i in range(DEG_NUM):
         #DEG=(i)*((360/DEG_NUM)/2)
         filtername=str(i+1)+'se.txt'
         se=np.loadtxt('filters/'+filtername)
         if(i==0):
             se=np.reshape(se,(1,len(se)))
         if(i==6):
             se=np.reshape(se,(len(se),1))
         se=se.astype('uint8')
         im_closei=cv2.dilate(im,se)
         im_closei=cv2.erode(im_closei,se)
         im_close.append(im_closei);
    imTemp39=im_close[0]
    for i in range(DEG_NUM-1):
        k=i+1
        imTemp39=np.minimum(imTemp39,im_close[k])

    marker=imcomplement(imTemp39)
    mask=imcomplement(im)
    imF=imreconstruct(marker,mask)
    imF=mat2gray(imF)
    imF=imcomplement(imF)
    return imF

def find_th(x):

    #mode= stats.mode(x)
    (_, idx, counts) = np.unique(x, return_index=True, return_counts=True)
    index = idx[np.argmax(counts)]
    mx = x[index]
    sx=np.std(x)
    thl=mx+3*sx
    thh=mx+4*sx
    return thl,thh
############ MAIN ##############

if len(sys.argv)<3:
    print('example usage : python crack_detection2 images_milestone2/1.jpg 50 ( optional images_milestone2/output/1.jpg)')

width_cm=int(sys.argv[2])

if len(sys.argv)==4:
    img_file_out=sys.argv[3]
else:
    img_file_out='output.png'


img_file=sys.argv[1]
print('processing '+img_file)
imgorig=cv2.imread(img_file)
size_orig=np.shape(imgorig)
print(size_orig)
## resize if the original size is different from dataset images
## so we can keep the same parameters for the filters
res=size_orig[1]/float(width_cm);
res_opt=65; # optimal resolution for bilateral filter
scale = float(res_opt)/float(res);
print(scale)

d=int(51/scale)
sigmaColor=int(201/scale)
sigmaSpace =int(201/scale);

if scale < 5:
    img_blur = cv2.bilateralFilter(imgorig ,d,sigmaColor,sigmaSpace)
else:
    img_blur =imgorig
##
res_opt=16 # optimal resolution for bilateral filter
scale = float(res_opt)/float(res)

img=cv2.resize(img_blur,(int(size_orig[1]*scale),int(size_orig[0]*scale)))
hsv=cv2.cvtColor(img,cv2.COLOR_BGR2HSV)
im=hsv[:,:,2]
bw_mask=np.zeros(np.shape(im))
bw_mask_offr=0;#round(np.shape(im)[0]/20)
bw_mask_offc=0;#round(np.shape(im)[1]/20)
bw_mask[bw_mask_offr:np.shape(im)[0]-bw_mask_offr, bw_mask_offc:np.shape(im)[1]-bw_mask_offc]=1;
im=mat2gray(im) #*mat2gray(bw_mask)

im=imcomplement(im)
im=im2double(im)
DEG_NUM=12;
LEN_c=11;
LEN_o=11;
LEN_diff=7;
ic1=reconstruction_by_dilation(im,LEN_c,DEG_NUM)
io1=min_openings(im,LEN_o,DEG_NUM)
iv=mat2gray(ic1-io1)
imDiff=smooth_cross_section(iv,LEN_diff,LEN_c)
imL=reconstruction_by_dilation(imDiff,LEN_c,DEG_NUM)
imF=reconstruction_by_erosion(imL,LEN_c,DEG_NUM)
F=np.squeeze(np.reshape(imF,(1,imF.size)))
TH_LOW,TH_HIGH=find_th(F)
#TH_LOW=0.12;
#TH_HIGH=0.2;
min_obj=5;
min_hole=10;
mask=np.zeros(np.shape(imF))
marker=np.zeros(np.shape(imF))
mask[imF>TH_LOW]=1
marker[imF>TH_HIGH]=1
bw_result=imreconstruct(marker,mask)
bw_result=filter_result3(img,bw_result,4,0.2)
bw_result=cv2.resize(bw_result,(size_orig[1],size_orig[0]))

imgr=imgorig[:,:,2];
imgr[bw_result>0]=255;
imgorig[:,:,2]=imgr;

print('saving output file: '+img_file_out)
cv2.imwrite(img_file_out,imgorig)
cv2.imwrite('output_bin.jpg',bw_result*255)
print('done ')


