<?php
/*
 * Tikapot i18n en Translation
 * By Keiouu
 *
 */

$i18n_data = array(	
	/* Tikapot Core */
	"error1" => "Error: ",
	"warn1" => "Warning: ",
	"noobjexist" => "No objects matching query exist",
	"multiobjexist" => "Multiple objects matching query exist",
	"fieldne" => "Field does not exist!",
	"nolongerpart" => "is no longer a part of",
	"shdin" => "should be in",
	"saveerror1" => "Error in save(): pre_save() returned false",
	"saveerror2" => "Error in save(): Model is not supposed to exist! (Perhaps you forgot to call parent constructor?) in",
	"saveerror3" => "::save(): model did not validate!",
	"mqerr1" => "Warning: Model is invalid!",
	"mqerr2" => "Warning: Query argument must be an array!",
	"mqerr3" => "is not a valid element in this query!",
	"sigerr1" => "Signal already registered!",
	"sigerr2" => "Signal isnt registered!",
	"sigerr3" => "Method doesnt exist in object!",
	"cacheerr1" => "Error in TPCache: You must have the Memcached PHP extension installed for this to work!",
	"pnfe" => "Page not found!",
	"hasres" => "has resource",
	"submit" => "Submit",
	"errorfile" => "Error uploading file.",
	"formerrctrl" => "Invalid Control Block!",
	"formerrcsrf" => "Invalid CSRF token found!",
	"formerrdata" => "Error In Data Reconstruction! Invalid field: ",
	"formerrsave" => "Error in Form.save()! You must supply a model to be able to save",
	"fielderr1" => "Error: Boolean did not validate: ",
	"fielderr2" => "Value is longer than max_length",
	"fielderr3" => "Error: Date is not in the format: YYYY-MM-DD",
	"fielderr4" => "Error: DateTime is not in the format: YYYY-MM-DD HH:MM:SS",
	"fielderr5" => "was not found!",
	"fielderr6" => "Error: Integer did not validate:",
	"fielderr7" => "M2M Form Fields are not implemented yet",
	"fielderr8" => "Error: More than 1 object!",
	"fielderr9" => "Precision must be valid: n,n",
	"fielderr10" => "Value is not numeric!",
	"captchaerr" => "Incorrect CAPTCHA entry!",
	"403" => "Sorry, you arent allowed to see that!",
	"404" => "Sorry, that page does not exist!",
	"500" => "Sorry, we are currently having some trouble displaying that page!",
	"dberr1" => "Error: Could not connect to the database server.",
	"dberr2" => "Error: the database is not connected!",
	
	/* Tikapot App */
	"comingsoon" => "Coming Soon!",
	"welcometp" => "Welcome to Tikapot!",
	"welcometp_desc" => "Tikapot is a brand new PHP framework built, on the latest technologies, to be lightweight and extremely flexible<br />Want to find out more?...",
	"welcometp_desc2" => "If you need some help getting started why not try one of these?",
	"welcometp_desc3" => "Not sure if Tikapot is right for you? Check out our showcase!",
	"tpsite" => "Tikapot Website",
	"tp_home" => "Home Page",
	"home" => "Home",
	"tpdownload" => "Download the Latest Version (RC3)!",
	"poweredby" => "Powered By",
	"copy" => "Copyright &copy; Tikapot.com",
	
	/* App Designer */
	"tp_appdesigner" => "Application Designer",
	
	/* Showcase */
	"tp_showcase" => "Tikapot Showcase",
	
	/* Tutorials App */
	"tp_tutorials" => "Tutorials",
	"tutorials_desc" => "Here you can find a few tutorials to help you get started.",
	"tutorial1" => "Introduction",
	"tutorial1d" => "<h2>What is Tikapot?</h2><p>Tikapot is a framework for building web applications. It could be used to develop a blog, a forum or something more unique…</p><p>Frameworks provide the “building blocks” for building applications, essentially like being given a strong foundation to build a house.. it gives you a head start.</p><h2>Why should I use it?</h2><p>Frameworks like Tikapot enable you to get your project finished in less time, as we all know the quicker you can get off the ground the better.</p><p>I wont try to sell Tikapot to you, it should be your choice. Tikapot is my pet project and will be here and free forever because I use it in all my personal web based projects and always will. That said, I do hope you like it!</p>",
	"tutorial2" => "Getting Started",
	"tutorial2d" => '<h2>Download Tikapot</h2><p>The first step in developing your new website/application, is to download Tikapot. At the moment your options simply include: <a title="Zip Format" href="https://github.com/Tikapot/Tikapot/zipball/master" target="_blank">zip</a> or <a title="Tar.gz Format" href="https://github.com/Tikapot/Tikapot/tarball/master" target="_blank">tar</a> format. If you use Windows, click &#8216;zip&#8217;, if you use Linux or Mac OSX click &#8216;tar&#8217;.</p><h2>Installation</h2><p>Installing is as simple as extracting the archive into your web folder, this may involve uploading and extracting it to your website through cPanel, or it may just mean extracting it to a folder (for example: /var/www/)</p><h2>Configuring</h2><p>Rename example_config.php to config.php and edit the variables. You will need to connect to either a PostgreSQL or a MySQL database (PostgreSQL is highly recommended).</p>',
	"tutorial3" => "Your First App!",
	"tutorial3d" => ' <h2>Creating the app</h2>
				<p>The first step is to go to the apps directory and create a new folder called "hello". Create a file called "init.php" in this new directory. Add the new app to the "$apps_list" array in config.php so it looks like this:</p>
				<pre>$apps_list = array("hello");</pre>
				<p>Now you are ready to start developing your application! Lets make a simple hello world app.</p>
				<h2>Creating the view...</h2>
				<p>The first step in making Tikapot show something on the screen is to create what we call a "View".<br />
				A "View" is a section of code that tells Tikapot what to display on the screen, and how to display it.<br />
				Create a file called "views.php" in the "/apps/hello/" folder and write the following:</p>
				<pre>&lt;?php
require_once(home_dir . "framework/view.php");
class HomeView extends View {
	public function render($request, $args) {
		print "Hello World";
	}
}
?&gt;</pre>
				<p>So what does that do? That is a view, a view is a class, when the page is displayed the "render" function is called which is supposed to do all of the work of actually showing the page. In this case the words "Hello World" are printed to the screen.</p>
				<h2>Adding the URL...</h2>
				<p>Okay so we have created our view... What next? Next we need to tell Tikapot when to use that view. We need to register a URL. A URL is a location, for example "http://www.tikapot.com/about" is a URL. So lets say anyone who visits out website should see our new HomeView. Create a file in "/apps/hello/" called "urls.php" and place the following code in it:</p>
				<pre>
&lt;?php
require_once(home_dir . "apps/hello/views.php");
new HomeView("/");
?&gt;</pre>And the last step is to edit "/apps/hello/init.php" to include our URLs:<pre>&lt;?php
require_once(home_dir . "apps/hello/urls.php");
?&gt;</pre>
				<p>Thats it! Anyone who visits the URL "/" (for example: "http://www.tikapot.com")  will see whatever HomeView wants them to see which, in this tutorial, is "Hello World".</p>',
	"tutorial4" => "Templates",
	"tutorial4d" => '<h2>Why use a template?</h2>
<p>We have already seen what can be done with views but what about HTML, Javascript, CSS? Surely you dont want to print() all of it? Of course not! Thats what template views are for..</p>
<h2>Creating the template...</h2>
<p>Create a folder called "templates" in the "/apps/hello/" folder, and in that create a file called "index.php". Open index.php and put the following in it:</p>
<pre>&lt;html&gt;
&lt;head&gt;
	&lt;title&gt;Hello&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
	&lt;p&gt;Hello World!&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</pre>

<h2>The URL...</h2>
<p>You remember our urls.php file? Open that and make it look like this:</p>
<pre>&lt;?php
require_once(home_dir . "framework/view.php");
require_once(home_dir . "apps/hello/urls.php");

new HomeView("/");
new View("/home/", home_dir . "apps/hello/templates/index.php");
?&gt;</pre>
<p>Thats it! Anyone who visits the URL "/home/" (for example: "http://www.tikapot.com/home/") will see our new web page!</p>',
	"tutorial5" => "Models",
	"tutorial5d" => "",
	"tutorial6" => "Forms",
	"tutorial6d" => "",
	"tutorial7" => "i18n",
	"tutorial7d" => "",
	"tutorial8" => "Media",
	"tutorial8d" => "",
	"tutorial9" => "Cache",
	"tutorial9d" => "",
);

?>

