
import numpy as np
import matplotlib.pyplot as plt
import cv2
from skimage import data
from skimage.feature import register_translation
from skimage.feature.register_translation import _upsampled_dft
from scipy.ndimage import fourier_shift
from skimage import transform as tf
import time
import os
import argparse
import video

def parse_args():
    """Parse input arguments."""
    parser = argparse.ArgumentParser(description='crack detection video')
    parser.add_argument('-v','--video',dest='video',help='video file',required=True)
    parser.add_argument('-o','--output',dest='output',help='Output file', default = 'output.png')
    parser.add_argument('-f','--frame',dest='frame',help='Frame file', default = 'frame.png')
    parser.add_argument('-b','--output_bin',dest='output_bin',help='Binary file', default = 'output_bin.png')
    parser.add_argument('-s','--output_stiching',dest='output_stiching',help='Stiching file', default = 'output_stiching.png')
    parser.add_argument('-t','--type',dest='type' ,default = '2')
    parser.add_argument('-w','--width',dest='width',default= '30')
    args = parser.parse_args()

    return args


def blend(imageA,imageB):

    if len(np.shape(imageA))==3:
          maskA=imageA[:,:,0]>0
          maskB=imageB[:,:,0]>0
          mask_diff=(1-maskB)*maskA
          result=imageB
          result[:,:,0]=imageB[:,:,0]+mask_diff*imageA[:,:,0]
          result[:,:,1]=imageB[:,:,1]+mask_diff*imageA[:,:,1]
          result[:,:,2]=imageB[:,:,2]+mask_diff*imageA[:,:,2]
          ra,ca=np.where(maskA>0)
          rb,cb=np.where(maskB>0)
          r1a=min(ra)
          r2a=max(ra)
          c1a=min(ca)
          c2a=max(ca)
          r1b=min(rb)
          r2b=max(rb)
          c1b=min(cb)
          c2b=max(cb)
          r1=min(r1a,r1b)
          r2=min(r2a,r2b)
          c1=min(c1a,c1b)
          c2=min(c2a,c2b)
          k=10;
          result=result[r1+k:r2-k,c1+k:c2-k,:]
    else:
          maskA=imageA>0
          maskB=imageB>0
          mask_diff=(1-maskB)*maskA
          result=imageB
          result=imageB+mask_diff*imageA
          ra,ca=np.where(maskA>0)
          rb,cb=np.where(maskB>0)
          r1a=min(ra)
          r2a=max(ra)
          c1a=min(ca)
          c2a=max(ca)
          r1b=min(rb)
          r2b=max(rb)
          c1b=min(cb)
          c2b=max(cb)
          r1=min(r1a,r1b)
          r2=min(r2a,r2b)
          c1=min(c1a,c1b)
          c2=min(c2a,c2b)
          k=1;
         # result=result[r1+k:r2-k,c1+k:c2-k]

    #mask_sum=maskA+maskB;

    return result

def image_remove_pad(image):
     if len(np.shape(image))==3:
          mask=image[:,:,0]>0
          ra,ca=np.where(mask>0)
          r1=min(ra)
          r2=max(ra)
          c1=min(ca)
          c2=max(ca)
          k=1
          image=image[r1+k:r2-k,c1+k:c2-k,:]
     else:
          mask=image>0
          ra,ca=np.where(mask>0)
          r1=min(ra)
          r2=max(ra)
          c1=min(ca)
          c2=max(ca)
          k=1
          image=image[r1+k:r2-k,c1+k:c2-k]
     return image
def image_pad(image,offr,offc):
    rows=np.shape(image)[0]
    cols=np.shape(image)[1]
    size = rows+offr, cols+offc
    image_BIG = np.zeros(size, dtype=np.uint8)
    image_BIG[int(offr/2) :int(offr/2)+rows,int(offc/2) :int(offc/2) +cols]=image
    return image_BIG

def find_bb_scale(image,scale):
        image =  cv2.resize(image,None,fx=scale, fy=scale, interpolation = cv2.INTER_NEAREST)
        ra,ca=np.where(image>0)
        ra=ra/scale
        ca=ca/scale
        r1=int(min(ra))
        r2=int(max(ra))
        c1=int(min(ca))
        c2=int(max(ca))
        return r1,c1,r2,c2
########################################
def image_remove_pad_corner(image,corner_r,corner_c):
     if len(np.shape(image))==3:
          mask=image[:,:,0]>0
          ra,ca=np.where(mask>0)
          r1=min(ra)
          r2=max(ra)
          c1=min(ca)
          c2=max(ca)
          corner_r=corner_r-r1
          corner_c=corner_c-c1
          k=0
          image=image[r1+k:r2-k,c1+k:c2-k,:]
     else:
          #mask=image>0
          r1,c1,r2,c2=find_bb_scale(image,0.4)
          k=2
          r1=r1-k
          r2=r2+k
          c1=c1-k
          c2=c2+k
          #ra,ca=np.where(image>0)
          #k=2
          #r1=min(ra)-k
          #r2=max(ra)+k
          #c1=min(ca)-k
          #c2=max(ca)+k
          corner_r=corner_r-r1
          corner_c=corner_c-c1
          k=0
          image=image[r1:r2,c1:c2]
     return image,corner_r,corner_c


def image_pad_corner(image,offr,offc,corner_r,corner_c):
    rows=np.shape(image)[0]
    cols=np.shape(image)[1]
    size = rows+offr, cols+offc
    image_BIG = np.zeros(size, dtype=np.uint8)
    image_BIG[int(offr/2) :int(offr/2)+rows,int(offc/2) :int(offc/2) +cols]=image
    corner_r=corner_r+int(offr/2)
    corner_c=corner_c+int(offc/2)
    return image_BIG,corner_r,corner_c

def blend_corner(imageP,image_new,shift,corner_r,corner_c):
    imageP,corner_r_new,corner_c_new=image_pad_corner(imageP,500,500,corner_r,corner_c)
    rs=corner_r_new-shift[0]
    cs=corner_c_new-shift[1]
    if len(np.shape(imageP))==3:
        imageP[int(rs):int(rs)+np.shape(image_new)[0], int(cs):int(cs)+np.shape(image_new)[1],:]=image_new
    else:
        imageP[int(rs):int(rs)+np.shape(image_new)[0], int(cs):int(cs)+np.shape(image_new)[1]]=image_new

    start_time = time.time()

    imageP,corner_r_new,corner_c_new= image_remove_pad_corner(imageP,rs,cs)
    print("%s seconds ---" % (time.time() - start_time))
    #corner_r_new=rs
    #corner_c_new=cs
    return imageP,corner_r_new,corner_c_new

def blend_corner2(imageP,image_new,shift,corner_r,corner_c,black):


    rs=corner_r-shift[0]
    cs=corner_c-shift[1]
    #print (np.shape(image_new)[0])

    rs2=int(rs)+np.shape(image_new)[0]
    cs2=int(cs)+np.shape(image_new)[1]
    W=np.shape(imageP)[1]
    H=np.shape(imageP)[0]
    Hn=H
    Wn=W

    origin_r=0
    origin_c=0
    if rs<0:
        Hn=H+abs(rs)
        origin_r=abs(rs)
        rs=0

    if cs<0:
        Wn=W+abs(cs)
        origin_c=abs(cs)
        cs=0
    if rs2>H:
        Hn=rs2
    if cs2>W:
        Wn=cs2


    if len(np.shape(imageP))==3:
        imagePn=np.zeros((Hn,Wn,3))
        imagePn[origin_r:origin_r+H,origin_c:origin_c+W]=imageP
        imagePn[int(rs):int(rs)+np.shape(image_new)[0], int(cs):int(cs)+np.shape(image_new)[1],:]=image_new
    else:
        imagePn=np.zeros((int(Hn),int(Wn)))
        imagePn[int(origin_r):int(origin_r+H),int(origin_c):int(origin_c+W)]=imageP
        if black==False:
            imagePn[int(rs):int(rs)+np.shape(image_new)[0], int(cs):int(cs)+np.shape(image_new)[1]]=image_new



    return imagePn,int(rs),int(cs)

def check_overlap(imageP,image_new,shift,corner_r,corner_c):
    rs=corner_r-shift[0]
    cs=corner_c-shift[1]
    rs2=int(rs)+np.shape(image_new)[0]
    cs2=int(cs)+np.shape(image_new)[1]
    W=np.shape(imageP)[1]
    H=np.shape(imageP)[0]
    Hn=H
    Wn=W

    origin_r=0
    origin_c=0
    if rs<0:
        Hn=H+abs(rs)
        origin_r=abs(rs)
        rs=0

    if cs<0:
        Wn=W+abs(cs)
        origin_c=abs(cs)
        cs=0
    if rs2>H:
        Hn=rs2
    if cs2>W:
        Wn=cs2

    imagePn=np.zeros((int(Hn),int(Wn)))
    imagePn[int(origin_r):int(origin_r+H),int(origin_c):int(origin_c+W)]=imageP
    noverlap= np.sum(imagePn[int(rs):int(rs)+np.shape(image_new)[0], int(cs):int(cs)+np.shape(image_new)[1]])
    overlap=noverlap/(np.shape(image_new)[0]*np.shape(image_new)[1])
    return overlap

def blend_corner3(imageP,image_new,shift,corner_r,corner_c,black):


    rs=corner_r-shift[0]
    cs=corner_c-shift[1]
    rs2=int(rs)+np.shape(image_new)[0]
    cs2=int(cs)+np.shape(image_new)[1]
    W=np.shape(imageP)[1]
    H=np.shape(imageP)[0]
    Hn=H
    Wn=W

    origin_r=0
    origin_c=0
    if rs<0:
        Hn=H+abs(rs)
        origin_r=abs(rs)
        rs=0

    if cs<0:
        Wn=W+abs(cs)
        origin_c=abs(cs)
        cs=0
    if rs2>H:
        Hn=rs2
    if cs2>W:
        Wn=cs2


    if len(np.shape(imageP))==3:
        imagePn=np.zeros((Hn,Wn,3))
        imagePn[origin_r:origin_r+H,origin_c:origin_c+W]=imageP
        imagePn[int(rs):int(rs)+np.shape(image_new)[0], int(cs):int(cs)+np.shape(image_new)[1],:]=image_new
    else:


        imagePn=np.zeros((int(Hn),int(Wn)))
        imagePn[int(origin_r):int(origin_r+H),int(origin_c):int(origin_c+W)]=imageP
        noverlap= np.sum(imagePn[int(rs):int(rs)+np.shape(image_new)[0], int(cs):int(cs)+np.shape(image_new)[1]])
        if black==False or noverlap==0:
            imagePn[int(rs):int(rs)+np.shape(image_new)[0], int(cs):int(cs)+np.shape(image_new)[1]]=image_new



    return imagePn,int(rs),int(cs)

def register_translation_scale(offset_image,image,scale):
    image =  cv2.resize(image,None,fx=scale, fy=scale, interpolation = cv2.INTER_CUBIC)
    offset_image =  cv2.resize(offset_image,None,fx=scale, fy=scale, interpolation = cv2.INTER_CUBIC)
    shift, error, diffphase = register_translation( offset_image,image)
    shift=shift/scale
    return shift, error, diffphase


if __name__ == "__main__":
    args = parse_args()
    video_src=args.video
    cam = video.create_capture(video_src)

    ret, frame = cam.read()
    image = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

    ret, frame = cam.read()
    offset_image_color=frame
    offset_image =cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

    width_cm=int(args.width)
    scale_reg=0.5
    ################################

    #print(np.shape(image)[0])
    #print(np.shape(image)[1])
    imageP=image
    imageP_bin=np.zeros((np.shape(image)[0],np.shape(image)[1]),'uint8')
    imageP_mask=np.ones((np.shape(image)[0],np.shape(image)[1]),'uint8')
    mask_frame=np.ones((np.shape(image)[0],np.shape(image)[1]),'uint8')
    corner_r=0
    corner_c=0
    ################################
    image=imageP[int(corner_r):int(corner_r)+np.shape(offset_image)[0],int(corner_c):int(corner_c)+np.shape(offset_image)[1] ]
    shift, error, diffphase = register_translation_scale( offset_image,image,scale_reg)
    corner_r_pre=corner_r
    corner_c_pre=corner_c
    imageP,corner_r,corner_c=blend_corner2(imageP,offset_image,shift,corner_r,corner_c,False)
    imageP_mask,tmp1,tmp2=blend_corner2(imageP_mask,mask_frame,shift,corner_r_pre,corner_c_pre,False)
    overlap=check_overlap(imageP_mask,mask_frame,shift,corner_r_pre,corner_c_pre)
    cv2.imwrite(args.frame,offset_image_color)
    #print(width_cm)
    if int(args.type)==2:
        os.system('/usr/local/bin/python3 scripts/video/crack_detection2_fast.py '+args.frame +' '+str(width_cm)+' '+args.output+' '+args.output_bin)
    else:
        os.system('/usr/local/bin/python3 scripts/video/crack_detection_fast.py '+args.frame)

    print (args.output_bin)
    crack=cv2.imread(args.output_bin,0)
    imageP_bin,tmp1,tmp2=blend_corner2(imageP_bin,crack,shift,corner_r_pre,corner_c_pre,False)


    #cv2.imwrite('imageP.png',imageP)
    #cv2.imwrite('imageP_bin.png',imageP_bin)
    #cv2.imwrite('imageP_mask.png',imageP_mask*255)
    ##############################
    step=1
    step_crackdetection=100#70
    th_overlap=0.3


    count_frame=1
    while ret==True:
        image=offset_image
        print('Frame : ' + str(count_frame))
        count_frame=count_frame+1
        ret, frame = cam.read()
        if ret==True:
            offset_image_color=frame
            offset_image =cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

            shift, error, diffphase = register_translation_scale( offset_image,image,scale_reg)
            overlap=check_overlap(imageP_mask,mask_frame,shift,corner_r,corner_c)
            #print(overlap)
            if  overlap<th_overlap:
                cv2.imwrite(args.frame,offset_image_color)

                if int(args.type)==2:
                     os.system('/usr/local/bin/python3 scripts/video/crack_detection2_fast.py '+args.frame+' '+str(width_cm)+' '+args.output+' '+args.output_bin)
                else:
                    os.system('/usr/local/bin/python3 scripts/video/crack_detection_fast.py '+args.frame)

                crack=cv2.imread(args.output_bin,0)

                corner_r_pre=corner_r
                corner_c_pre=corner_c

                imageP,corner_r,corner_c=blend_corner2(imageP,offset_image,shift,corner_r,corner_c,False)
                imageP_bin,tmp1,tmp2=blend_corner2(imageP_bin,crack,shift,corner_r_pre,corner_c_pre,False)
                imageP_mask,tmp1,tmp2=blend_corner2(imageP_mask,mask_frame,shift,corner_r_pre,corner_c_pre,False)
                #cv2.imwrite('imageP.png',imageP)#debug
                #cv2.imwrite('imageP_bin.png',imageP_bin)#debug
                #cv2.imwrite('imageP_mask.png',imageP_mask*255)#debug
            else:
                corner_r_pre=corner_r
                corner_c_pre=corner_c
                imageP,corner_r,corner_c=blend_corner2(imageP,offset_image,shift,corner_r,corner_c,True)
                imageP_bin,tmp1,tmp2=blend_corner2(imageP_bin,offset_image,shift,corner_r_pre,corner_c_pre,True)
                imageP_mask,tmp1,tmp2=blend_corner2(imageP_mask,mask_frame,shift,corner_r_pre,corner_c_pre,True)

    ##############################
    cv2.imwrite(args.frame,offset_image_color)

    if int(args.type)==2:
         os.system('/usr/local/bin/python3 scripts/video/crack_detection2_fast.py '+args.frame+' '+str(width_cm)+' '+args.output+' '+args.output_bin)
    else:
         os.system('/usr/local/bin/python3 scripts/video/crack_detection_fast.py '+args.frame)

    crack=cv2.imread(args.output_bin,0)

    corner_r_pre=corner_r
    corner_c_pre=corner_c
    imageP,corner_r,corner_c=blend_corner2(imageP,offset_image,shift,corner_r,corner_c,False)
    imageP_bin,tmp1,tmp2=blend_corner2(imageP_bin,crack,shift,corner_r_pre,corner_c_pre,False)
    imageP_mask,tmp1,tmp2=blend_corner2(imageP_mask,mask_frame,shift,corner_r_pre,corner_c_pre,False)

    #############################



    #############################
    cv2.imwrite(args.output_bin,imageP_bin*255)

    img_out=np.zeros((np.shape(imageP)[0],np.shape(imageP)[1],3),'uint8')
    img_out[:,:,0]=imageP
    img_out[:,:,1]=imageP
    img_out[:,:,2]=imageP

    imgr=img_out[:,:,2];
    imgr[imageP_bin>0]=255;
    img_out[:,:,2]=imgr;
    cv2.imwrite(args.output_stiching,imageP)
    cv2.imwrite(args.output,img_out)
    print('done ')
