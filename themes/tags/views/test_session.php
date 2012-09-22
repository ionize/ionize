<h1>Session</h1>
<p>
	This tag uses the Session library of CodeIgniter.<br/>
	See : <a href="http://codeigniter.com/user_guide/libraries/sessions.html">http://codeigniter.com/user_guide/libraries/sessions.html</a>
</p>
<ion:tree_navigation />

<hr/>

<h2>Increment one value in Session</h2>

<pre>
</pre>

<h3>Result</h3>

<!-- The test has to be done with "false", as the CI lib returns FALSE when the var isn't set. -->
<ion:session:get key="my_var" is="false">
	<p class="red">"my_var" is not set.</p>
	<p>Reload the page to set it was just set.</p>
	<ion:session:set key="my_var" value="1" />
</ion:session:get>
<ion:else>

</ion:else>

<p>
Session value : <b><ion:session:get key="my_var" /></b>
</p>

<h2>Advanced usage</h2>

<p>Browser : <ion:browser:session:set key="browser_version" method="browser" /></p>

<p>Browser from session : <ion:session:get key="browser_version"/></p>