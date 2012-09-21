<h1>Conditions : &lt;ion:if /></h1>
<p>The "if" tag expands the HTML between the tag if the result of the expression is true</p>
<ion:tree_navigation />

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
                &lt;ion:if key="index" expression="(index+1)%2==0">
    				&lt;/ul>
    				&lt;ul class="boxes">
        		&lt;/ion:if>
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
				<ion:if key="index" expression="(index+1)%2==0">
					</ul>
					<ul class="boxes">
				</ion:if>
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
        &lt;ion:article:if key="name" expression="name == 'article-30'">
            &lt;p class="red">Youpiii ! &lt;/p>
        &lt;/ion:article:if>
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
		<ion:article:if key="name" expression="name == 'article-30'">
			<p class="red">Youpiii ! </p>
		</ion:article:if>
		<ion:article:else>
			<p>... no youpi ...</p>
		</ion:article:else>
	</ion:articles>
</ion:page>

<hr />


<h2>Test 3 : Multiple keys</h2>
	
<pre>
&lt;ion:page id="3">
	&lt;p>Page ID : &lt;b>&lt;ion:id/>&lt;/b>&lt;/p>
	&lt;p>Page name : &lt;b>&lt;ion:name/>&lt;/b>&lt;/p>

	&lt;ion:if key="name,id_page" expression="name == 'test-page' && id_page==3">
		&lt;p class="red">Youpiii ! &lt;/p>
	&lt;/ion:if>
&lt;/ion:page>
</pre>

<h3>Result</h3>

<ion:page id="3">
	<p>Page ID : <b><ion:id/></b></p>
	<p>Page name : <b><ion:name/></b></p>

	<ion:if key="name,id_page" expression="name == 'test-page' && id_page==3">
		<p class="red">Youpiii ! </p>
	</ion:if>
</ion:page>

<hr />

<h2>Test 3 : Error in expression</h2>
<p>
	The condition has an error ("=" instead of "==")<br/>
	It outputs one error message<br/>
</p>

<pre>
&lt;ion:page id="3">
	&lt;ion:if key="name" expression="name = 2">
		&lt;p class="red">Youpiii ! &lt;/p>
	&lt;/ion:if>
&lt;/ion:page>
</pre>

<h3>Result</h3>

<ion:page id="3">
	<ion:if key="name" expression="name = 2">
		<p class="red">Youpiii ! </p>
	</ion:if>
</ion:page>
