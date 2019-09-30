# World of Warcraft Character Sheet Template

## Overview
 I've been looking for a fun project that would allow me to play around with APIs. The idea came to me to create a World of Warcraft character sheet when I ran across the Blizzard API documentation. The page pulls all data from the Community and Profile APIs and formats it for the page.

 The template makes use of the Advanced Custom Fields plugin for WordPress. This allows a simple way for a useer to apply their credentials without hard coding it into the system. Doing so is possible, but this way I can share the code without giving away sensitive information.

 ## Installation and Configuration
 Installing the code requires FTP access to your WordPress site and some knowledge on how to compile SASS files. Once you download the files, connect to your WordPress site via FTP, and navigate to your theme's directory. Once there, upload the img folder and its contents into the root of your theme along with wow-profile-page.php. For the styling you have some options. I've included a SASS partial with all of the styles I used. You can compile it into your style.css (which is what I did), compile it into its own CSS file and include that file in your functions.php, take the styling from the partial and translate it yourself into either style.css or your own stylesheet. You can also just throw away the styling and come up with your own... it's really up to you.

 Once the files are all in place, make sure the Advanced Custom Fields plugin is installed and import the ACF Export JSON file. 


 ## Future Implementation
 This would really be better suited as a plugin instead of a page template, so I might convert it over as a plugin at some future date.


 ## Author
 Julien A. Chambers
 https://www.pdxchambers.com