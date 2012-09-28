<h1>Lang tag</h1>

<p>
	This tag replaces the <b>&lt:ion:translation /></b> tag, which becomes deprecated.<br/>
	For compatibility reasons, <b>&lt:ion:translation /></b> stays available.
</p>
<hr/>

<h2>Simple string, with tag set substring</h2>
<pre>
&lt;ion:lang key="lang_tag_string_1" swap="My tag defined string part" />
</pre>
	
<ion:lang key="lang_tag_string_1" swap="My tag defined string part" />


<h2>Display string with dynamic data</h2>
<pre>
&lt;ion:page:lang key="lang_tag_string_2" swap="page, page::title" />
</pre>

<ion:page:lang key="lang_tag_string_2" swap="page, page::title" />


<h2>Display string with dynamic data from website</h2>

<p>
	In this example, we use positioning of swapped string : <b>%s$1</b> and <b>%s$2</b></p>
<p>
	That means that in the attribute <b>swap="global::site_title, user::email"</b>, "site_title" is the 1st string, "email" the 2nd.
</p>
<p>
	<b class="red">Important</b><br/>
	When using positioning, take care your that string in the language file is wrapped by single quotes and not double quotes.<br/>
	Using positioning and double quotes will fire one PHP error.<br/>
	This is due to the used native PHP function.
</p>
<pre>
&lt;!-- In your language file : -->
$lang['lang_tag_string_3'] = 'This is the website title : &lt;b class="red">%1$s&lt;/b>, and if you are logged in, your email is : &lt;b class="green">%2$s&lt;/b> !';

&lt;!-- In the view : -->
&lt;ion:user:lang key="lang_tag_string_3" swap="global::site_title, user::email" />
</pre>

<ion:user:lang key="lang_tag_string_3" swap="global::site_title, user::email" />


<h2>Display string with dynamic data from multiple parent tags</h2>
<pre>
&lt;ion:page:user:lang key="lang_tag_string_4" swap="global::site_title, user::email" />
</pre>

<ion:page:user:lang key="lang_tag_string_4" swap="global::site_title, user::email" />
