
<h1>&lt;ion:languages />, &lt;ion:language /></h1>

<h2>Language menu</h2>

<pre>
&lt;ion:languages tag="ul">
	&lt;li>
		&lt;a href="&lt;ion:language:url />">
			&lt;ion:language:id /> : &lt;ion:language:url />&lt;ion:language:active> &lt;span class="red">(is active)&lt;/span>&lt;/ion:language:active>
		&lt;/a>
	&lt;/li>
&lt;/ion:languages>
</pre>

<h3>Result</h3>

<ion:languages tag="ul">
	<li>
		<a href="<ion:language:url />">
			<ion:language:id /> : <ion:language:url /><ion:language:is_active> <span class="red">(is active)</span></ion:language:is_active>
		</a>
	</li>
</ion:languages>


<hr/>


<h2>Standalone &lt;ion:language /></h2>

<p>Used as standalone tag, returns the current language</p>

<pre>
&lt;p>Current lang code : 		&lt;ion:language:code />&lt;/p>
&lt;p>Current lang name : 		&lt;ion:language:name />&lt;/p>
&lt;p>Current lang online : 	&lt;ion:language:get key="online" />&lt;/p>
&lt;p>Current lang default : 	&lt;ion:language:get key="def" />&lt;/p>

&lt;p>Current lang is default ?
	&lt;ion:language:is_default>Yes, it is !&lt;/ion:language:is_default>
	&lt;ion:language:is_default is="false">No, it is not !&lt;/ion:language:is_default>
&lt;/p>
</pre>

<h3>Result</h3>

<p>Current lang code : 		<ion:language:code /></p>
<p>Current lang name : 		<ion:language:name /></p>
<p>Current lang online : 	<ion:language:get key="online" /></p>
<p>Current lang default : 	<ion:language:get key="def" /></p>
<p>Current lang url : 		<ion:language:url /></p>

<p>Current lang is default ?
	<ion:language:is_default>Yes, it is !</ion:language:is_default>
	<ion:language:is_default is="false">No, it is not !</ion:language:is_default>
</p>



