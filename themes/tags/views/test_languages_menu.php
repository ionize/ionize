
<h1>&lt;ion:languages />, &lt;ion:language /></h1>


<ion:tree_navigation />


<hr/>


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

<ion:languages tag="ul">
	<li>
		<a href="<ion:language:url />">
			<ion:language:id /> : <ion:language:url /><ion:language:is_active> <span class="red">(is active)</span></ion:language:is_active>
		</a>
	</li>
</ion:languages>


<hr/>


<h2>Standalone &lt;ion:language /></h2>

<p>Used as standalone tag, returns the current language.</p>

<pre>
&lt;p>Current lang code : 		&lt;b>&lt;ion:language:code />&lt;/b>&lt;/p>
&lt;p>Current lang name : 		&lt;b>&lt;ion:language:name />&lt;/b>&lt;/p>
&lt;p>Current lang online : 	&lt;b>&lt;ion:language:online />&lt;/b>&lt;/p>
&lt;p>Current lang default ? : &lt;b>&lt;ion:language:default />&lt;/b>&lt;/p>
&lt;p>Current lang url : 		&lt;b>&lt;ion:language:url />&lt;/b>&lt;/p>
&lt;p>Current lang is default ?
    &lt;ion:language:default is="true">&lt;b>Yes, it is !&lt;/b>&lt;/ion:language:default>
    &lt;ion:language:default is="false">&lt;b>No, it is not !&lt;/b>&lt;/ion:language:default>
&lt;/p>
</pre>

<p>Current lang code : 		<b><ion:language:code /></b></p>
<p>Current lang name : 		<b><ion:language:name /></b></p>
<p>Current lang online : 	<b><ion:language:online /></b></p>
<p>Current lang default ? : <b><ion:language:default /></b></p>
<p>Current lang url : 		<b><ion:language:url /></b></p>
<p>Current lang is default ?
    <ion:language:default is="true"><b>Yes, it is !</b></ion:language:default>
    <ion:language:default is="false"><b>No, it is not !</b></ion:language:default>
</p>


<h2>Language menu with helper</h2>

