from PIL import Image, ImageDraw, ImageFilter, ImageChops
import numpy
def getbox(im, color):
    bg = Image.new(im.mode, im.size, color)
    diff = ImageChops.difference(im, bg)
    diff = ImageChops.add(diff, diff, 2.0, -100)
    return diff.getbbox()

def split(im):
    retur = []
    emptyColor = im.getpixel((0, 0))
    box = getbox(im, emptyColor)
    width, height = im.size
    pixels = im.getdata()
    sub_start = 0
    sub_width = 0
    offset = box[1] * width
    for x in range(width):
        if pixels[x + offset] == emptyColor:
            if sub_width > 0:
                retur.append((sub_start, box[1], sub_width, box[3]))
                sub_width = 0
            sub_start = x + 1
        else:
            sub_width = x + 1
    if sub_width > 0:
        retur.append((sub_start, box[1], sub_width, box[3]))
    return retur

im1 = Image.open('/home/balasaravanan/Desktop/test_py_images/3596_cc2400_032_62U.png')

im = Image.open("/home/balasaravanan/Desktop/test_py_images/2Crave_NX2_MB_2C6976830864_12.png")

for idx, box in enumerate(split(im)):
    im.crop(box) 



# im2 = Image.open('/home/balasaravanan/Desktop/test_py_images/trim_0.png')


back_im = im1.copy()
back_im.paste(im, (100, 50))
back_im.save('/home/balasaravanan/Desktop/test_py_images/final1.png', quality=95)  