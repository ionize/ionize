<h1>Lang tag</h1>

<p>
	This tag replaces the <b>&lt:ion:translation term="" /></b> tag, which becomes deprecated.<br/>
	For compatibility reasons, <b>&lt:ion:translation /></b> remains available.
</p>
<p>
	Language files are located in the folder : <b>/themes/your_theme/language/xx/</b>
</p>
<p>
	Languages string can be retrieved through tags but also through the Javascript <b>"Lang"</b> object.<br/>
	In this case the javascript <b>Lang</b> object must be build by calling the <b>jslang</b> tag.
</p>
<hr/>

<h2>Simple string, with tag set substring</h2>
<p>
	In this example, we call the string of the key <b>lang_tag_string_1</b> declared in the file <b>/themes/tags/language/xx/tags_lang.php</b>.
	<br/>
	And we replace <b>%s</b> with <b>"My tag defined string part"</b>.
</p>
<pre>
&lt;ion:lang key="lang_tag_string_1" swap="My tag defined string part" />
</pre>
	
<ion:lang key="lang_tag_string_1" swap="My tag defined string part" />


<h2>Display string with dynamic data</h2>
<p>
    <b>&lt;ion:page:lang /></b> : Get first the current page and then call the lang tag.<br/>
</p>
<p>
	This makes the current page data available for the lang tag.<br/>
	In the string <b>lang_tag_string_2</b> which has 2 <b>%s</b>, we replace <b>%s</b> with the asked data from page
</p>
<pre>
&lt;!-- In your language file : -->
$lang['lang_tag_string_2'] = 'This data comes from the page which has the ID &lt;b class="red">%s&lt;/b> and the title &lt;b class="green">%s&lt;/b>';

&lt;!-- In the view : -->
&lt;ion:page:lang key="lang_tag_string_2" swap="page::id_page, page::title" />
</pre>

<ion:page:lang key="lang_tag_string_2" swap="page::id_page, page::title" />


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


<h2>Get lang string from Javascript object</h2>

<p>
	Notice : The string swap of dynamical data is not yet available when using the Javascript <b>Lang</b> object.

</p>

<pre>
&lt;ion:jslang framework="jQuery" object="Lang" />

&lt;div id="jsLang">&lt;/div>

&lt;script type="text/javascript">
    $('#jsLang').html(
            Lang.get(
                    'lang_tag_string_js'
            )
    );
&lt;/script>
</pre>
<ion:jslang framework="jQuery" object="Lang" />

<div id="jsLang"></div>

<script type="text/javascript">
	$('#jsLang').html(
			Lang.get(
				'lang_tag_string_js'
			)
	);
</script>
