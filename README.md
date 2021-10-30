# Homomm
## Every person its messages..

Hello and welcome to Homomm!   
	   
Homomm is a light and simple software on premise to exchange multimedia messages with friends.  
	   
Homomm is released under GPLv3 license, it is supplied AS-IS and we do not take any responsibility for its misusage.  
	   
Homomm name comes from the two words, "homines" meaning our choise to give chance to the human beings to come first, and "mm" for "multimedia messaging".  
     
Homomm doesn't want to be a replacement of Whats App, Telegram, Wechat, etc. but simply want to be their alter ago.   
     
First step, use the left side panel password and salt fields to create the hash to insert in the config file. Remember to manually set there also the salt value.  
	   
As you are going to run Homomm in the PHP process context, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:  

<ol>
<li>Check the permissions of your "Repo" folder in your web app private path; and set its path in the config file.</li>
<li>In the Repo path create a "user" folder for each user and give to this folder the write permission. Set it appropriately in the config file.</li>
<li>In the config file, set every "user" information appropriately like in the examples given.</li>
<li>Configure your <a href="http://twilio.com" style="color:#e6d236;">Twilio</a> account information appropriately to send out sms notification.</li>	      
<li>Configure the max history items as required (default: 50).</li>	      
</ol>	
     
Hope you can enjoy it and let us know about any feedback: <a href="mailto:info@homomm.org" style="color:#e6d236;">info@homomm.org</a>
	   
 ![Homomm in action](/Public/static/res/screenshot2.jpg)
