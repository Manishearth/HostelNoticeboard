# Hostel Noticeboard App
# By Manish Goregaokar
#
# Tested on Ubuntu
# Meant to run on Raspian


from Tkinter import *
from threading import *
from PIL import Image, ImageTk
import json
import time
import os
import sys
import datetime
class App:

    def __init__(self, master):
        print "Starting Noticeboard: " + (datetime.datetime.fromtimestamp(int("1284101485")).strftime('%Y-%m-%d %H:%M:%S'))
        
        self.config={'directories':['Cult','Sports','Tech','Hostel'],
        'delimiter':"         ",
        'tickerspeed':[1,10], # x pixels per y milliseconds
        'tickerpad':[0,2], #lower, upper
        'tickerstyle':['white',("Helvectica", "18")], #fill,font(face,size)
        'canvasbg':'black',
        'tickerrectcolor':'red',
        'picspeed':2000, #Switch images every x seconds
        'picsatatime':4, # 1,2, or 4
        'tilingpad':[2,2], #horiz,vert
        'refreshcount':{1:10,2:10,4:10} #After how many iterations ought I refresh? [PAT1,PAT2,PAT3]
        }
        try:
            x=open('config.json')
            self.config=json.loads(x.read())
            x.close()
        except Exception as e:
            print "Error loading config, using default config"
            print e
        
        
        self.tickerlist=""
        self.piclist=[]
        self.piclist2={}
        self.itercount=0
        self.frame = Frame(master)
        self.frame.pack()
        master.geometry("{0}x{1}+0+0".format(master.winfo_screenwidth(), master.winfo_screenheight()))
        self.windowd=[master.winfo_screenwidth(), master.winfo_screenheight()]
        #master.wm_state(ZOOMED)
        self.rt=master
        self.rt.overrideredirect(True)
        self.can = Canvas(self.rt, bg="black",borderwidth=0,highlightthickness=0,background=self.config['canvasbg'])
        self.can.pack(expand=True,fill=BOTH)
        self.can.bind('<Double-1>',self.close)
        self.getticker()

        self.tickertext=self.can.create_text(self.can.canvasx(0),self.can.canvasy(0),text=self.tickerlist,  fill=self.config['tickerstyle'][0], font=self.config['tickerstyle'][1])
        a=self.can.bbox(self.tickertext)
        self.imgbbox=[self.windowd[0],self.windowd[1]-self.config['tickerpad'][0]-self.config['tickerpad'][1]-(a[3]-a[1])]
        self.getpiclist()
        self.tickerrect=self.can.create_rectangle(0,self.imgbbox[1],self.windowd[0],self.windowd[1],fill=self.config['tickerrectcolor'])
        self.can.tag_lower(self.tickerrect)
        print "Recommended image size: "+str(self.imgbbox[0])+"x"+str(self.imgbbox[1])+" (Screen size: "+str(self.windowd[0])+"x"+str(self.windowd[1])+")"
        self.can.move(self.tickertext,-a[0],self.windowd[1]+a[1]-self.config['tickerpad'][1])
        self.tickerstate=0
        
        if self.config['picsatatime']==1:
            self.picindex=[0]
            self.imgs=[self.can.create_image(self.imgbbox[0]/2,self.imgbbox[1]/2,image=self.photos[0],anchor=CENTER)]
        elif self.config['picsatatime']==2:
            self.picindex=[0,0]

            self.imgs=[0,0]
            self.imgs[0]=self.can.create_image(self.imgbbox[0]/4-self.config['tilingpad'][0],self.imgbbox[1]/2,image=self.photos[self.config['directories'][0]][self.picindex[0]],anchor=CENTER)
            self.imgs[1]=self.can.create_image(3*self.imgbbox[0]/4+self.config['tilingpad'][0],self.imgbbox[1]/2,image=self.photos[self.config['directories'][1]][self.picindex[1]],anchor=CENTER)
        elif self.config['picsatatime']==4:
            self.picindex=[0,0,0,0]

            self.imgs=[0,0,0,0]
            
            self.imgs[0]=self.can.create_image(self.imgbbox[0]/4-self.config['tilingpad'][0],self.imgbbox[1]/4-self.config['tilingpad'][1],image=self.photos[self.config['directories'][0]][self.picindex[0]],anchor=CENTER)
            self.imgs[1]=self.can.create_image(3*self.imgbbox[0]/4+self.config['tilingpad'][0],self.imgbbox[1]/4-self.config['tilingpad'][1],image=self.photos[self.config['directories'][1]][self.picindex[1]],anchor=CENTER)
            self.imgs[2]=self.can.create_image(self.imgbbox[0]/4-self.config['tilingpad'][0],3*self.imgbbox[1]/4+self.config['tilingpad'][1],image=self.photos[self.config['directories'][2]][self.picindex[2]],anchor=CENTER)
            self.imgs[3]=self.can.create_image(3*self.imgbbox[0]/4+self.config['tilingpad'][0],3*self.imgbbox[1]/4+self.config['tilingpad'][1],image=self.photos[self.config['directories'][3]][self.picindex[3]],anchor=CENTER)
        master.after(self.config['tickerspeed'][1],self.moveticker)
        master.after(self.config['picspeed'],self.movepic)
        


    def moveticker(self):
        
        self.can.move(self.tickertext,-self.config['tickerspeed'][0],0)

        a=self.can.bbox(self.tickertext)
        if self.tickerstate is 1:
            self.can.move(self._tickertext,-self.config['tickerspeed'][0],0)
            a=self.can.bbox(self._tickertext)
            if a[0]<0:
                self.can.delete(self.tickertext)
                self.tickertext=self._tickertext
                self.tickerstate=0
        elif a[2]<self.windowd[0] and self.tickerstate is 0:
            self.getticker()
            self.tickerstate=1
            self._tickertext=self.can.create_text(self.can.canvasx(0),self.can.canvasy(0),text=self.config['delimiter']+self.tickerlist, fill=self.config['tickerstyle'][0], font=self.config['tickerstyle'][1])
            a=self.can.bbox(self._tickertext)
            self.can.move(self._tickertext,self.windowd[0]-a[0],self.windowd[1]+a[1]-self.config['tickerpad'][0])
        self.rt.after(self.config['tickerspeed'][1],self.moveticker)



    def movepic(self):
        self.itercount+=1
        for i in range(0,len(self.imgs)):
            self.can.delete(self.imgs[i])
        if self.itercount==self.config['refreshcount'][str(self.config['picsatatime'])]:
            self.getpiclist()
            self.itercount=0    
        if self.config['picsatatime']==1:
            self.picindex[0]+=1
            if self.picindex[0] >= len(self.piclist):
                self.picindex=[0]
            self.imgs[0]=self.can.create_image(self.imgbbox[0]/2,self.imgbbox[1]/2,image=self.photos[self.picindex[0]],anchor=CENTER)
        elif self.config['picsatatime']==2:
            for i in range(0,2):
                self.picindex[i]+=1
                if self.picindex[i]>=len(self.piclist2[self.config['directories'][i]]): 
                    self.picindex[i]=0
            self.imgs[0]=self.can.create_image(self.imgbbox[0]/4-self.config['tilingpad'][0],self.imgbbox[1]/2,image=self.photos[self.config['directories'][0]][self.picindex[0]],anchor=CENTER)
            self.imgs[1]=self.can.create_image(3*self.imgbbox[0]/4+self.config['tilingpad'][0],self.imgbbox[1]/2,image=self.photos[self.config['directories'][1]][self.picindex[1]],anchor=CENTER)
        elif self.config['picsatatime']==4:
            for i in range(0,4):
                self.picindex[i]+=1
                if self.picindex[i]>=len(self.piclist2[self.config['directories'][i]]): 
                    self.picindex[i]=0
                    #self.getpiclist()
            self.imgs[0]=self.can.create_image(self.imgbbox[0]/4-self.config['tilingpad'][0],self.imgbbox[1]/4-self.config['tilingpad'][1],image=self.photos[self.config['directories'][0]][self.picindex[0]],anchor=CENTER)
            self.imgs[1]=self.can.create_image(3*self.imgbbox[0]/4+self.config['tilingpad'][0],self.imgbbox[1]/4-self.config['tilingpad'][1],image=self.photos[self.config['directories'][1]][self.picindex[1]],anchor=CENTER)
            self.imgs[2]=self.can.create_image(self.imgbbox[0]/4-self.config['tilingpad'][0],3*self.imgbbox[1]/4+self.config['tilingpad'][1],image=self.photos[self.config['directories'][2]][self.picindex[2]],anchor=CENTER)
            self.imgs[3]=self.can.create_image(3*self.imgbbox[0]/4+self.config['tilingpad'][0],3*self.imgbbox[1]/4+self.config['tilingpad'][1],image=self.photos[self.config['directories'][3]][self.picindex[3]],anchor=CENTER)
        self.rt.after(self.config['picspeed'],self.movepic)



    def getticker(self):
        temp=[]
        try:
            for i in self.config['directories']:
                #print [os.listdir(i)]
                temp+=[open(os.path.join(i,f)).read() for f in os.listdir(i) if f.endswith('.txt')]
            self.tickerlist=self.config['delimiter'].join(temp).replace('\n','') if temp !=[] else self.tickerlist
        except IOError as e:
            print "I/O error({0}): {1}".format(e.errno, e.strerror)


    def getpiclist(self):

        t2={}
        temp=[]
        try:
            for i in self.config['directories']:
                #print [os.listdir(i)]
                t2[i]=[os.path.join(i,f) for f in os.listdir(i) if (f.endswith('.png') or f.endswith('.jpg') or f.endswith('.gif'))]
                temp+=t2[i]
            x=False
            if self.piclist==temp:
                x=True
            self.piclist2=t2 if t2 !={} else self.piclist2
            self.piclist = temp if temp!=[] else self.piclist
            if t2!={} and not x:
                
                if self.config['picsatatime']==1:
                    self.photos=[]
                    for i in range(0,len(temp)):
                        self.photos+= [Image.open(temp[i])]
                        self.photos[i].thumbnail((self.imgbbox[0],self.imgbbox[1]))
                        self.photos[i] = ImageTk.PhotoImage(self.photos[i])
                elif self.config['picsatatime']==2:
                    self.photos={}
                    for i in self.config['directories']:
                        self.photos[i]=[]
                        for j in range(0,len(t2[i])):
                            self.photos[i]+=[Image.open(t2[i][j])]
                            self.photos[i][j].thumbnail((self.imgbbox[0]/2-self.config['tilingpad'][0],self.imgbbox[1]))
                            self.photos[i][j] = ImageTk.PhotoImage(self.photos[i][j])
                elif self.config['picsatatime']==4:
                    self.photos={}
                    for i in self.config['directories']:
                        self.photos[i]=[]
                        for j in range(0,len(t2[i])):
                            self.photos[i]+=[Image.open(t2[i][j])]
                            self.photos[i][j].thumbnail((self.imgbbox[0]/2-self.config['tilingpad'][0],self.imgbbox[1]/2-self.config['tilingpad'][1]))
                            self.photos[i][j] = ImageTk.PhotoImage(self.photos[i][j])
            print "Image fetch&parse successful"
        except IOError as e:
            print "I/O error({0}): {1}".format(e.errno, e.strerror)


    def close(self,event):
        self.frame.quit()

root = Tk()

app = App(root)

root.mainloop()