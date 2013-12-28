HostelNoticeboard
=================

Electronic noticeboard for IIT Bombay hostels. Meant to run on a Raspberry Pi.
##Requirements

Python Tkinter and Python Imaging Library
```
sudo apt-get install python-tk python-imaging
```

##Usage
Fill subdirectories with .txt files for tickers, and .png, .jpg, or .gif images. Then run gui.py with python 2.7.

When you run gui.py, it outputs the recommended image size. Please try to make your images use this (or set their background to the canvas background).

##Configuration

```javascript
{
   "tickerspeed":[1,10], //ticker will move 1 pixel every 10 ms
   "tilingpad":[2,2], //When tiling, pad each image at the center with [horizontal,vertical] padding
   "delimiter":"         ", //What to use between two ticker items
   "canvasbg":"black", //background of canvas. All unused space will be this color
   "picsatatime":4, /*How many pics at a time? Allowed values: 1,2,4. With 1, 
                    it will loop through all the directories and show the images. 
                    With 2, it will display one panel for the first directory, and 
                    another for the second directory, and loop through each individually. 
                    If there are more than two directories, it will take whichever one is 
                    specified first in the 'directories' configuration key. With 4 pics at a time, the screen is tiled 2x2, and 
                    the first four directories get their own space. Subsequent directories are grouped with the corresponding 
                    directory from the first four
                    */
   "refreshcount":{"1":10,"2":10,"4":10}, // For given value of picsatatime, after how many iterations should the list be reloaded?
   "picspeed":2000, //Persist the pic for this long (in ms)
   "directories":[ //Directories in which there are tickers/pics
      "Cult",
      "Sports",
      "Tech",
      "Hostel"
   ],
   "tickerpad":[0,2], //How much to pad the ticker area on the top and bottom
   "tickerstyle":["white",["Helvectica","18"]], // The style of ticker text, specified as [color,[font face,font size]]
   "tickerrectcolor":"red" //Color of the red rectangle around the ticker
}

```

##Typical directory setup
```
|-- gui.py
|-- config.json
|-- README.md
|-- Cult
|   |-- cult1.png
|   |-- ticker2.txt
|   |-- insync.png
|   |-- cult2.png
|   `-- ticker.txt
|-- Hostel
|   |-- abc.png
|   |-- def.png
|   |-- poster final 2.jpg
|   |-- tickercanteen.txt
|   |-- tickerdosa.txt
|   `-- elections.jpg
|-- Sports
|   |-- RIS-POS-2.jpg
|   |-- Schedule.jpg
|   |-- sport.png
|   |-- Tech GC 3 Scoring.jpg
|   |-- ticker1.txt
|   |-- ticker2.txt
|   `-- vintage-poster.jpg
`-- Tech
    |-- Arpit 1.jpg
    |-- arpit 2.jpg
    |-- arpit 3.jpg
    |-- electrify.png
    |-- tickertech.txt
    `-- xlr8.png
```
