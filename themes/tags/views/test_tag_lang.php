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
<pre>
&lt;ion:user:lang key="lang_tag_string_3" swap="global::site_title, user::email" />
</pre>

<ion:user:lang key="lang_tag_string_3" swap="global::site_title, user::email" />


<h2>Display string with dynamic data from multiple parent tags</h2>
<pre>
&lt;ion:page:user:lang key="lang_tag_string_4" swap="global::site_title, user::email" />
</pre>

<ion:page:user:lang key="lang_tag_string_4" swap="global::site_title, user::email" />
