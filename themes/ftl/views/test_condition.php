<h1>Conditions : Use of expressions</h1>
<p>
	Tests can be done when using one value tag.<br/>
	Remind : "Value" tags are supposed to return one value, by opposition to "Loop" tags, which are
	used to loop inside a data collection.
</p>

<hr/>

<h2>Test 1 : Expression with one integer value</h2>
<p>
	Medias are displayed inside one UL HTML element.<br/>
	Each 2 medias, we want to close this UL and open a new one<br/>
	Each media has a field called "index", starting at 0.<br/>
	Modulo 2 of this value + 1 will return true every 2 pictures.<br/>
</p>

<pre>
&lt;ion:page>
    &lt;ul class="boxes">
        &lt;ion:medias type="picture">
            &lt;ion:media size='200' square='true'>
                &lt;li>
                    &lt;img src="&lt;ion:src size='200' />" />
                &lt;/li>
                &lt;ion:index expression="(index+1)%2==0">
    				&lt;/ul>
    				&lt;ul class="boxes">
        		&lt;/ion:index>
        	&lt;/ion:media>
        &lt;/ion:medias>
    &lt;/ul>
&lt;/ion:page>
</pre>

<h3>Result</h3>

<ion:page>
	<ul class="boxes">
		<ion:medias type="picture">
			<ion:media size='200' square='true'>
				<li>
					<img src="<ion:src size='200' />" />
				</li>
				<ion:index expression="(index+1)%2==0">
					</ul>
					<ul class="boxes">
				</ion:index>
			</ion:media>
		</ion:medias>
	</ul>
</ion:page>

<hr />

<h2>Test 2 : Expression with one string</h2>
<p>
	The page 3 has one article which has the code "article-30".<br/>
	We will check the code for each article, and if it is "article-30", we display "Youpiii"<br/>
</p>
<pre>
&lt;ion:page id="3">
    &lt;ion:articles>
        &lt;ion:article:title tag="h3"/>
        &lt;ion:article:name expression="=='article-30'">
            &lt;p class="red">Youpiii ! &lt;/p>
        &lt;/ion:article:name>
        &lt;ion:article:else>
            &lt;p>... no youpi ...&lt;/p>
        &lt;/ion:article:else>
    &lt;/ion:articles>
&lt;/ion:page>
</pre>

<h3>Result</h3>

<ion:page id="3">
	<ion:articles>
		<ion:article:title tag="h3"/>
		<ion:article:name expression="=='article-30'">
            <p class="red">Youpiii ! </p>
		</ion:article:name>
		<ion:article:else>
			<p>... no youpi ...</p>
		</ion:article:else>
	</ion:articles>
</ion:page>

<hr />



<h2>Test 3 : Error in expression</h2>
<p>
	The condition has an error ("=" instead of "==")<br/>
	It outputs one error message<br/>
</p>

<pre>
&lt;ion:page id="3">
    &lt;ion:name expression="=2">
        &lt;p class="red">Youpiii ! &lt;/p>
    &lt;/ion:name>
&lt;/ion:page>
</pre>

<h3>Result</h3>

<ion:page id="3">
	<ion:name expression="=2">
        <p class="red">Youpiii ! </p>
	</ion:name>
</ion:page>
