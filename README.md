# Trip Out

![Trip Out Logo](https://tripout.tk/images/logo.png)

TripOut is a PHP web application for finding events or day outs near you. 

This project is for OCR A level Computer Science Coursework. If you want to read the write up for this project:

https://docs.google.com/document/d/1-S_DqnB8ipWi1Mw2CZCW0NPlL5Vu8c95W82xq1r6vhk/


Database structure

| Table name     	| Description                            	|
|----------------	|----------------------------------------	|
| Events         	| Stores event information               	|
| Images         	| Stores image for each user             	|
| LinkedEditable 	| Has which fields are editable in Users 	|
| Users          	| Stores user information                	|

TripOut has a dynamic content structure, which allows for more datatypes to be added, as well as an editable order of content.
It currently supports HTML content using a custom Rich Text Editor and Images, which are stored in the "Images" table for each user, that can then be looked up.
