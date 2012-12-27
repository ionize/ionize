    <!-- FOOTER -->
    <footer id="footer">
		<div class="container">
        <div class="row-fluid">
            <ion:page id="footer">
				<ion:articles>
					<ion:article>
						<div class="span4 first">
							<ion:title tag="h4" />
							<ion:content />
						</div>
					</ion:article>
				</ion:articles>
            </ion:page>
        </div>
        </div>
    </footer>

    <!-- Default Theme Js Files & Codes -->
    <script type="text/javascript" src="<ion:theme_url />assets/js/default.min.js"></script>
    <script type="text/javascript" src="<ion:theme_url />assets/js/jquery.easing.1.3.js"></script>
    <script type="text/javascript">
        !function ($) {
            var $window = $(window);

            // Disable certain links in docs
            $('[href^=#]').click(function (e) {
                e.preventDefault()
            })

        }(window.jQuery)
    </script>
</body>
</html>