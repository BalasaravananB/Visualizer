import cv2 
import numpy as np 
import codecs, json 
import sys 


from detecto import core, utils, visualize


# import argparse
# # construct the argument parser and parse the arguments
# ap = argparse.ArgumentParser()
# ap.add_argument("-i", "--image", required = True, help = "Path to the image")
# args = vars(ap.parse_args())

# load the image, clone it for output, and then convert it to grayscale
args = sys.argv
image = args[1]
baseurl = args[2]
carid = args[3]

width = 668
# 800

height =501
# 600

# baseurl = '/bala/projects/inoru/WheelVisualizer/'
storageurl = '/storage/custom-detection-dataset/'

dummyPath =baseurl+storageurl+"/resized/"

img = cv2.imread(image, cv2.IMREAD_COLOR) 

# img = cv2.resize(img, (0, 0), fx = 0.1, fy = 0.1) 
img = cv2.resize(img, (width, height))
cv2.imwrite(dummyPath+carid+'_car.png', img)



# Specify the path to your image
image = utils.read_image(dummyPath+carid+'_car.png')# Specify the path to your image
model = core.Model.load(baseurl+storageurl+'model_weights.pth', ['wheel']) 

#Getting Prediction Values 
predictions = model.predict(image)

# # predictions format: (labels, boxes, scores)
labels, boxes, scores = predictions
# visualize.show_labeled_image(image, boxes, labels)
 
  
# # Blue color in BGR 
color = (255, 0, 0) 
  
# # Line thickness of 2 px 
thickness = 2   

points = [];

for key, value in enumerate(boxes): 
        if scores[key] > 0.6 : 
                rect = value.tolist()
                startX = rect[0]
                startY = rect[1]
                endX = rect[0]
                endY = rect[1]
                x = (rect[0] +rect[2])/2
                y = (rect[1] +rect[3])/2
                w = (rect[2]-rect[0])
                h = (rect[3]-rect[1])
                points.append([x,y,w,h,startX,startY,endX,endY])
#               print(points)
                # cv2.rectangle(img,(value[0],value[1]),(value[2],value[3]), color, thickness)
                # cv2.circle(img, (value[0],value[1]), 1, color, 5)
                # cv2.circle(img, (value[2],value[3]), 1, color, thickness)
#
# cv2.imwrite(dummyPath+carid+'_car.png', img)

print(points)
# 

# # cv2.imshow('img', img)
# # cv2.waitKey(0) 


