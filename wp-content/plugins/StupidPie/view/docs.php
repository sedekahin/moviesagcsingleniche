<?php 

?>
 <div class="bootstrap-wpadmin">
    <div class="row">
      <div class="span12">
          <div class="page-header">
            <center><img height="90px" src="<?php echo plugins_url( 'images/StupidPie.png' , __FILE__ );?>"></center>
          </div>
          
      </div>
    </div>
    <div class="row">

      <div class="span9">
        <ul class="nav nav-tabs">
            <li class="active"><a target="_blank" href="#instalasi" data-toggle="tab">Instalasi</a></li>
            <li><a target="_blank" href="#faq" data-toggle="tab">FAQ</a></li>
            <li><a target="_blank" href="#changelog" data-toggle="tab">Changelog</a></li>
            <li><a target="_blank" href="#bonus" data-toggle="tab">Bonus</a></li>
        </ul>
        <div class="row">
          <div class="span9">
            <div class="tab-content">
              <div class="tab-pane active" id="instalasi">
                <div class="page-header"><h4>Basic penggunaan StupidPie</h4></div>
                
                  <ol>
                    <li>Upload dan aktifkan pluginnya</li>
                    <li>Kopi paste kode
                  <pre>&lt;?php echo spp(get_search_query());?&gt;</pre>
                  taruh pada file search.php themes wordpress Anda. Masukkan kode diatas sebelum code
                  <pre>if have_post();</pre>
                  </li>
                    <li>Kopi paste kode
                  <pre>[spp_random_terms count=10]</pre>
                  di sidebar wordpress, widget text.</li>
                    <li>Secara default kode
                  <pre>&lt;?php echo spp(get_search_query());?&gt;</pre>
                  memanggil template default.html check pada folder (StupidPie\templates\default.h<wbr />tml)</li>
                  </ol>
                <div class="page-header"><h4>Advanced</h4></div>
                Contoh di atas adalah contoh penggunaan tingkat dasar. StupidPie sendiri sebenarnya bisa memakai 3 parameter saat dipanggil. Secara default, spp memanggil parameter keyword, template dan hack:
<pre>&lt;?php
$keyword = get_search_query();
$template = 'default.html';
$hack = '';
echo spp($keyword, $template, $hack);
// bisa disingkat echo spp($keyword);
?&gt;</pre>
<h5>Keyword</h5>
Keyword adalah satu-satunya parameter yang wajib diisi. Karena beda tempat beda cara dapatkan keywordnya. Sebagai contoh, di single.php keyword bisa didapatkan dengan cara:
<pre>$keyword = single_post_title( '', false );</pre>
Kalau di halaman search.php:
<pre>$keyword = get_search_query();</pre>
<h5>Template</h5>
Template adalah tempat kita mengatur tampilan hasil ambil data. Lokasinya di folder StupidPie/templates. Kita bisa membuat template sendiri atau memodifikasi dari yang sudah ada. Template StupidPie memakai standar h2o template engine jadi kalau ada waktu untuk mempelajari syntax templatenya, bisa melihat dokumentasi lebih jelas untuk <a target="_blank" title="h2o template engine" href="http://www.h2o-template.org/">template h2o</a>.

Untuk memanggil template, bisa dimasukkan ke parameter kedua. Misal:
<pre>&lt;?php
$keyword = get_search_query();
$template = 'amazon.html';
$hack = '';
echo spp($keyword, $template, $hack);
?&gt;</pre>
<h5>Hack</h5>
Hack adalah parameter ketiga yang jarang dipakai namun cukup keren. Dengan memanfaatkan hack, kita bisa membuat hampir semua jenis AGC. Misal, pdf, ppt, doc, amazon, ehow, dll. Fungsinya sendiri semacam filter. Sebagai contoh:
<pre>&lt;?php
$keyword = get_search_query();
$template = 'wikipedia.html'; // semisal kita bikin template sendiri untuk wikipedia
$hack = 'site:en.wikipedia.org'; // hack ini akan menampilkan konten HANYA dari en.wikipedia.org
echo spp($keyword, $template, $hack);
?&gt;</pre>
Contoh lain untuk pdf search engine:
<pre>&lt;?php
$keyword = get_search_query();
$template = 'pdf.html'; // semisal kita bikin template sendiri untuk pdf
$hack = 'filetype:pdf'; // hack ini akan menampilkan konten HANYA yang berakhiran .pdf
echo spp($keyword, $template, $hack);
?&gt;</pre>
              </div>
              <div class="tab-pane" id="faq">
                <div class="page-header"><h4>Pertanyaan yang sering diajukan</h4></div>
                <ul>
  <li>Ada setting lain yang bisa diutak-atik ndak?<br>
Coba liat file <a target="_blank" href="plugin-editor.php?file=StupidPie%2Fsettings.php&plugin=StupidPie%2Fstupidpie.php">StupidPie/settings.php</a></li>
<li>Gimana cara nambahin bad words biar diblacklist?<br>
Coba edit $bad_words di file <a target="_blank" href="plugin-editor.php?file=StupidPie%2Fsettings.php&plugin=StupidPie%2Fstupidpie.php">StupidPie/settings.php</a></li>
<li>Dapet komplain DMCA nih, ada cara ngatasin?<br>
Coba masukkan kata-kata ke $bad_words di file <a target="_blank" href="plugin-editor.php?file=StupidPie%2Fsettings.php&plugin=StupidPie%2Fstupidpie.php">StupidPie/settings.php</a> atau pake plugin <a target="_blank" href="http://wordpress.org/extend/plugins/redirection/">Redirection</a>, redirect aja ke homepage url yang dikomplain.</li>
  <li>Gimana kalau themeku ndak ada search.php?<br>
Coba kopi index.php ke file search.php dan edit.</li>
  <li>Kata ente redirect itu ampuh buat indexing. Kodenya plis?<br>
<pre>RewriteEngine on
RewriteCond %{HTTP_HOST} ^webutama\.com$ [NC]
RewriteRule ^(.*)$ http://subdomain.domainente.com/<wbr />$1</a> [R,L]</pre>
</li>
  <li>Gw ditendang nih ma hosting A. Gimana dong, ada rekomendasi pake hosting apa?<br>
Syukurin. Dibilangin jangan main AGC.
Eh, yang jelas banyak opsi hosting lain. <a href="http://goo.gl/Ng2Jy" title="Hosting Kuat Hostgator">Hostgator</a> katanya kuat sampai 10K/day, Beberapa teman memakai <a href="http://goo.gl/yOehW" title="VPS Kuat Knownhost">VPS Knownhost</a>, aku pribadi menggunakan <a href="http://goo.gl/kE6R4" title="Hosting Hawkhost">Hawkhost</a> dan <a href="http://goo.gl/kKYVe" title="Hosting Kuat Indonesia">Webhostnix</a>.</li>
  <li>Situs gw trafficnya meledak nih. Ada cara optimasi speednya ndak?<br>
By default, spp sudah banter dan ringan. Namun nggak ada salahnya optimasi biar tahan banting:
- <a target="_blank" href="http://wordpress.org/extend/plugins/w3-total-cache/">w3 total cache</a>, aktifkan db cache, object cache dan browser cache. Jangan aktifkan page cache.
- Daftarin ke <a target="_blank" href="https://www.cloudflare.com/my-websites.html">CloudFlare</a>, aktifkan CDN + Basic Caching. Cukup make yang free.</li>
  <li>Gimana caranya biar web baruku keindex dengan cepat?<br>
Redirect paling ampuh, ping lebih lamban. Atau bisa mencoba <a target="_blank" title="Cara indexing" href="http://googlewebmastercentral.blogspot.com/2011/08/submit-urls-to-google-with-fetch-as.html">fetch as google bot</a>.</li>
  <li>Apa resiko main AGC?<br>
Deindex, banned adsense, diusir hosting. Itu hal paling sering yang dialami pemain agc.</li>
  <li>Bisa ndak SPP dipakai buat AGC Amazon, PDF, PPT, DOC, wiki, eHow, mp3?<br>
Bisa.</li>
  <li>Ane ada pertanyaan lain nih, dimana tanyanya?<br>
Via grup <a target="_blank" href="http://www.facebook.com/groups/ninjaplugins/">NinjaPlugins</a> dong.</li>
  <li>Invite ane ke grup dong?<br>
Kontak aja mastah <a target="_blank" href="http://www.facebook.com/moeghan/">Moeghan</a>.</li>
  <li>Gw pingin pamer earning + traffic. Boleh?<br>
Boleh :D, jangan lupa makan2.</li>
</ul>
              </div>
              <div class="tab-pane" id="changelog">
                <div class="page-header"><h4>Changelog History</h4></div>
                <?php echo nl2br('## 1.6 Release Notes:
* Fix "Call to a member function xpath() on a non-object" bug
* Fix "Fatal error " Bug
* Fix "parser error : Entity \'nbsp\' not defined" bug
* Optimize "Order By Rand()" in SQL
* Sebelum nyimpen term, ditrim + lower case biar ndak banyak makan space DB
* Nambah fungsi permalink statis, random dan dinamis
* Nambah style
* Nambah dokumentasi+admin panel

## 1.5 Release Notes:
* Added Bing Image RSS, StupidPie now able to fetch images ＼(^ω^＼)
* Added Youtube video template ＼(^ω^＼)
* Modify default template so it includes video and SEO Friendly images ＼(^ω^＼)

## 1.4 Release Notes:
* Bing API deprecated, now using Bing RSS
* Since we are using Bing RSS, StupidPie no longer able to fetch image (ㄒ_ㄒ)
* Since we are using Bing RSS, We no longer limited to 5000 query per month ＼(^ω^＼)

## 1.1 Release Notes:
* Private release
* Limit google bot indexing

## 1.0 Release Notes:
* Private release
* Improvement: remove Search Term Tagging 2 & SimplePie dependencies
* Modular design

## 0.7 Release Notes
* Improvement: Bad Keywords
* Improvement: encoded link out

## 0.5 Release Notes
* Initial Release

')?>
              </div>
              <div class="tab-pane" id="bonus">
<ul>
	<li>Jika ingin memakai permalink statis seperti <span style="color: #ff0000;">domain.com/tag/keyword1-keyword2</span></li>
</ul>
Buka file <strong>/Stupidpie/templates/widget.html</strong> lihat kode:
<pre>&lt;a href="{{ term.term | build_permalink_for 0 }}"&gt;{{ term.term }}&lt;/a&gt;</pre>
Pastikan setelah build_permalink_for nilainya 0 "Nol"
<br><br>
<ul>
	<li>Jika ingin memakai permalink random seperti <span style="color: #ff0000;">domain.com/abcdefg/keyword1-keyword2</span></li>
</ul>
Buka file <strong>/<strong>Stupidpie/</strong>templates/widget.html</strong> lihat kode:
<pre>&lt;a href="{{ term.term | build_permalink_for 1 }}"&gt;{{ term.term }}&lt;/a&gt;</pre></span>
Pastikan setelah build_permalink_for nilainya 1 "Satu"
<br><br>
<ul>
	<li>Jika ingin memakai permalink dinamis seperti <span style="color: #ff0000;">domain.com/keyword1/keyword1-keyword2-keyword3</span></li>
</ul>
Buka file <strong>/<strong>Stupidpie/</strong>templates/widget.html</strong> lihat kode <pre>&lt;a href="{{ term.term | build_permalink_for 2 }}"&gt;{{ term.term }}&lt;/a&gt;</pre>
Pastikan setelah build_permalink_for nilainya 2 "Dua"
<br><br>
                <div class="page-header"><h4>Bonus!!!</h4></div>
                <img src="http://www.picunyu.com/rc.jpg" alt="dota"/>
              </div>
            </div>
          </div>
        </div>
      </div>
        
      <div class="span3">
        <div class="well">
          <a target="_blank" id="postnow" href="plugin-editor.php?file=StupidPie%2Fsettings.php&plugin=StupidPie%2Fstupidpie.php" class="btn btn-primary btn-large">Ubah Setting</a>
        </div>
        <div class="">
          <div class="page-header"><h3>Sponsor</h3></div>
          <ul class="nav nav-tabs nav-stacked">
            <?php
            $sponsors = '<li><a target="_blank" href="http://imcashmachine.com" title="Belajar WSO"><i class="icon-chevron-right"></i> Belajar WSO Yuk</a></li>
            <li><a target="_blank" href="http://is.gd/stupidpie" title="Get 50% Off Discount With Coupon "StupidPie""><i class="icon-chevron-right"></i> Get 50% Off Discount With Coupon "StupidPie"</a></li>
            <li><a target="_blank" href="http://wordpress.org/extend/plugins/seo-alrp/" title="SEO Autolinks & Related Posts"><i class="icon-chevron-right"></i>SEO Autolinks & Related Posts</a></li>
      			<li><a target="_blank" href="http://www.goblogthemes.com/gosense/" title="GOSENSE: Theme AGC Keren"><i class="icon-chevron-right"></i> GOSENSE: Theme AGC Keren</a></li>
      			<li><a target="_blank" href="http://www.webhostnix.com" title="Indonesian Litespeed Webhosting. Support SPP/AGC!"><i class="icon-chevron-right"></i> Webhostnix - Support SPP/AGC</a></li>
      			<li><a target="_blank" href="http://belajarbersamadani.com/agc-amazon-blog-service/" title="Dapatkan AGC Amazon Blog Service Gratis"><i class="icon-chevron-right"></i> Dapatkan AGC Amazon Blog Service Gratis</a></li>
      			<li><a target="_blank" href="http://www.infobisniskeren.net/" title="Peluang Bisnis Global"><i class="icon-chevron-right"></i> Peluang Bisnis Global</a></li>
      			<li><a target="_blank" href="http://romicakra.com/" title="Blog Internet Marketing"><i class="icon-chevron-right"></i> Blog Internet Marketing oleh Romi Cakra</a></li>
				    <li><a target="_blank" href="http://webdijual.com/" title="Website Brokerage & Consultations"><i class="icon-chevron-right"></i> WebDijual.com</a></li>';
            $sponsors = explode("\n", $sponsors);
            shuffle($sponsors);
            for ($i=0; $i < 5; $i++) { 
              echo $sponsors[$i];
            }

            ?>
          </ul>
          <div class="page-header"><h3>Bookmark</h3></div>
          <ul class="nav nav-tabs nav-stacked">
            <li><a target="_blank" href="http://www.facebook.com/groups/ninjaplugins/"><i class="icon-chevron-right"></i> Grup NinjaPlugins</a></li>
            <li><a target="_blank" href="http://www.facebook.com/moeghan/"><i class="icon-chevron-right"></i> Blom Diinvite Kegrup? Hub. Mastah Moeghan</a></li>
            <li><a target="_blank" href="http://www.facebook.com/photo.php?fbid=165583400249756&set=a.116780798463350.17497.100003942914968&type=1&comment_id=344041&ref=notif&notif_t=photo_comment&theater"><i class="icon-chevron-right"></i> All in One AGC Pack</a></li>
            
          </ul>
          <div class="page-header"><h3>Tutorial Kontribusi</h3></div>
          <ul class="nav nav-tabs nav-stacked">
            <li><a target="_blank" href="http://www.facebook.com/groups/ninjaplugins/doc/552872624740889/"><i class="icon-chevron-right"></i> Setting Permalink SPP Tanpa TAG (Sigit Agitsan)</a></li>
            <li><a target="_blank" href="http://www.facebook.com/groups/ninjaplugins/doc/508130549215097/"><i class="icon-chevron-right"></i> Daftar Bad Keywords Terbaru (Ariwolu Jisportal)</a></li>
            <li><a target="_blank" href="http://www.facebook.com/groups/ninjaplugins/doc/512639625430856/"><i class="icon-chevron-right"></i> Panen Keywords Untuk Inject SPP (Ivan Slackers)</a></li>
            <li><a target="_blank" href="http://www.facebook.com/groups/ninjaplugins/doc/556240747737410/"><i class="icon-chevron-right"></i> SPP buat AGC 404 (Andrie Bee Ratapu)</a></li>
            <li><a target="_blank" href="http://www.facebook.com/groups/ninjaplugins/permalink/562708980423920/"><i class="icon-chevron-right"></i> Breadcrumb (Mymoen Doank)</a></li>
            <li><a target="_blank" href="http://www.facebook.com/groups/ninjaplugins/512436298784522/"><i class="icon-chevron-right"></i> Amazon dengan SPP (Lin Fhiau) </a></li>
            <li><a target="_blank" href="http://www.facebook.com/groups/ninjaplugins/permalink/505774532784032/"><i class="icon-chevron-right"></i> PDF, PPT, DOC Search Engine (Ivan Slackers) </a></li>
            <li><a target="_blank" href="http://www.facebook.com/groups/ninjaplugins/files/"><i class="icon-chevron-right"></i> Tutorial Lainnya </a></li>
            
          </ul>
         
        </div>
      </div>
        
    </div>
    <?php global $current_user; 
  
get_currentuserinfo() ; 
if($current_user->allcaps['administrator']):
?>
<script id="IntercomSettingsScriptTag">
  var intercomSettings = {
    // TODO: The current logged in user's email address.
    email: "<?php echo $current_user->data->user_email; ?>",
    // TODO: The current logged in user's sign-up date as a Unix timestamp.
    created_at: <?php echo strtotime($current_user->data->user_registered);?>,
    app_id: "m9y7r9ro",
    custom_data: {
       site_url: '<?= site_url(); ?>',
    }
  };
</script>
<script>(function(){var w=window;var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://api.intercom.io/api/js/library.js';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}})();</script>

<?php endif;