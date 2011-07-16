
<h4 class="tweet">Ionize on twitter</h2>

<script src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
new TWTR.Widget({
  version: 2,
  type: 'profile',
  rpp: 3,
  interval: 6000,
  width: 'auto',
  height: 300,
  theme: {
    shell: {
      background: 'none',
      color: '#5f5f5f',
      links: '#0066CC'
    },
    tweets: {
      background: 'none',
      color: '#5f5f5f',
      links: '#0066CC'
    }
  },
  features: {
    scrollbar: false,
    loop: false,
    live: false,
    hashtags: true,
    timestamp: false,
    avatars: false,
    behavior: 'all'
  }
}).render().setUser('ionizecms').start();
</script>
