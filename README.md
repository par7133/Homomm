# Homomm

Hello and welcome to Homomm!   
	   
Homomm is a light and simple software on premise to exchange multimedia messages with friends, whats app like.  

Note*: Please contact me directly to have access to Homomm dev installation.  
  
Note**: I invite anyone from China and not who has access to Homomm.org to mirror it. If you get any problem downloading Homomm, please contact me directly.  
  
## What do we need to make Homomm successful?
<ol>
<li>Design / programming forces</li>  
<li>Website mirrors</li>   
<li>Money to give away <a href="http://twilio.com" style="color:#e6d236;"o>Twilio</a> sms for free to users.</li>  
<li>Social media advertisement</li>  
<li>Merchandising</li>  
<li>etc.</li>  
</ol>
	
What 5 Mode can offer in exchange:  
- % of the income from support/installation service  
- free advertisement on all the websites produced.  

If you want access to Homomm on my dev installation: code@gaox.io.

## License
 
Homomm is released under GPLv3 license, it is supplied AS-IS and we do not take any responsibility for its misusage.  
	
## Name and purpose   
   
Homomm name comes from the two words: "homines" meaning our choise to give chance to the human beings to come first and "mm" for multimedia messaging.  
     
Homomm doesn't want to be a replacement of Whats App, Telegram, Wechat, etc. but their alter ago.   

## Installation

Please refer to <a href="INSTALLATION.md" style="color:#e6d236;">Installation</a>

## Configuration  
    
First step, use the left side panel password and salt fields to create the hash to insert in the config file for every user. Remember to manually set there also the salt value.   

As you are going to run Homomm in the PHP process context, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:  

<ol>
<li>Check the permissions of your "Repo" folder in your web app private path; and set its path in the config file.</li>
<li>In the Repo path create a "user" folder for each user and give to this folder the write permission. Set it appropriately in the config file.</li>
<li>Check the permissions of your "hmm-img" folder in your web app public path; and set its path in the config file.</li>  
<li>In hmm-img path create a "user" folder for each user and give to this folder the write permission. Set it appropriately in the config file.</li>  	
<li>In the config file, set every "user" information appropriately like in the examples given.</li>
<li>Configure your <a href="http://twilio.com" style="color:#e6d236;">Twilio</a> account information appropriately to send out sms notification.</li>	      
<li>Configure the server pushing interval to be notified on new chat messages.</li>
<li>Configure the max history items as required (default: 50).</li>	      
</ol>	

Login with the password to message.

For any need of software additions, plugins and improvements please write to <a href="mailto:info@5mode.com">info@5mode.com</a>  

To help please donate by clicking <a href="https://gaox.io/l/dona1">https://gaox.io/l/dona1</a> and filling the form.  
     
## Screenshots
	   
 ![Homomm on desktop](/Public/static/res/screenshot1.png)   
     
 ![Homomm on mobile](/Public/static/res/screenshot2.png)


Feedback: <a href="mailto:code@gaox.io">code@gaox.io</a>

