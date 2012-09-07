
<h1>&lt;ion:if /></h1>
<p>Conditional tag : Expand the HTML between the tag if the result of the condition is true</p>

<h2>Test 1 : Condition with one integer value</h2>
<p>
	Medias are displayed inside one UL HTML element.<br/>
	Each 2 medias, we want to cloase this UL and open a new one<br/>
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
				&lt;ion:if key="index" condition="(index+1)%2==0">
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
				<ion:if key="index" condition="(index+1)%2==0">
					</ul>
					<ul class="boxes">
				</ion:if>
			</ion:media>
		</ion:medias>
	</ul>
</ion:page>


<h2>Test 1 : Condition with one string</h2>
<p>
	The page 3 has one article which has the code "article-30".<br/>
	We will check the code for each article, and if it is "article-30", we display "Youpiii"<br/>
</p>
<ion:page id="3">
	<ion:articles>
		<ion:article:title tag="h3"/>
		<ion:article:if key="name" condition="'name' = 'article-30'">
			<p class="red">Youpiii ! </p>
		</ion:article:if>
		<ion:article:else>
			<p>...</p>
		</ion:article:else>
	</ion:articles>
</ion:page>
