<?php
require('includes/core.php');
Page::header('Frequently Asked Questions - '.SITE_NAME.'','Know about the answers people ask mostly','faq,frequently,asked,questions,how,to,'.strtolower(SITE_NAME).'');
?>
<h1>Frequently Asked Questions</h1>
<p><strong>Question:</strong> What is content?<br/>
<strong>Answer: </strong> Here in this site, content refers TV Series and Movies.</p>
<p><strong>Question:</strong> How to download content?<br/>
<strong>Answer: </strong> In the download and info page, you will see two buttons for downloading. Try both link, at least one of them should work. If doesn't work either of them, that means those files are removed from the host server. If you find any content like this, please report them as broken link. You will find "report broken link" option right after the download links.</p>
<p><strong>Question:</strong> Watch online option not working, why?<br/>
<strong>Answer: </strong> If you face view online option is not workig even after waiting for few minutes, that means link is broken. Try to download and then watch locally if view online option is not working for you.</p>
<p><strong>Question:</strong> What is wishlist?<br/>
<strong>Answer: </strong> Wishlist is a list where you can bookmark any content so that you can watch or download them at anytime easily. Wishlist can save your time because you don't have to search for them. You will get all of your favourite contents in one place.<br/>
<u>Note:</u> You must be logged in to use wishlist feature.</p>
<p><strong>Question:</strong> What is request?<br/>
<strong>Answer: </strong> Request is feature where you can ask for a specific movie or tv series to upload. When admins comes online, they will process your request as soon as possible. However there are some limitations applicable. For example: you need to be logged in to make a request and you can't make more than one request per day. More info can be found on the request page.</p>
<p><strong>Question:</strong> What is BRRip, WebRip and all other RIPs?<br/>
<strong>Answer:</strong> There are many rips available in our site. RIPs are basically content release types. Here are the list of some common RIPs:<br>
<ul class="browser-default">
<li>DVD Rip: A DVD-Rip is a final retail version of a film, typically released before it is available outside its originating region. Often after one group of pirates releases a high-quality DVD-Rip, the "race" to release that film will stop. The release is an AVI file and uses the XviD codec (some in DivX) for video, and commonly mp3 or AC3 for audio. Because of their high quality, DVD-Rips generally replace any earlier copies that may already have been circulating. Widescreen DVDs used to be indicated as WS.DVDRip. DVDMux differs from DVDRips as they tend to use the x264 codec for video, AAC or AC3 codec for audio and multiplex it on a .mp4/.mkv file.</li>
<li>Web Download: WEB-DL (P2P) refers to a file losslessly ripped from a streaming service, such as Netflix, Amazon Video, Hulu, Crunchyroll, Discovery GO, BBC iPlayer, etc., or downloaded via an online distribution website such as iTunes. The quality is quite good, since they are not reencoded. The video (H.264 or H.265) and audio (AC3/AAC) streams are usually extracted from the iTunes or Amazon Video and remuxed into a MKV container without sacrificing quality. An advantage with these releases is that, like BD/DVDRips, they usually have no onscreen network logos. HDRips are typically transcoded versions of HDTV or WEB-DL source files, but may be any type of HD transcode.</li>
<li>Web Rip: In a WEB-Rip (P2P), the file is often extracted using the HLS or RTMP/E protocols and remuxed from a TS, MP4 or FLV container to MKV.</li>
<li>Blu-ray/BD/BRRip: Blu-ray or Bluray rips are encoded directly from the Blu-ray disc to 1080p or 720p (depending on disc source), and use the x264 or x265 codec. They can be ripped from BD25 or BD50 discs (or UHD Blu-ray at higher resolutions). BDRips are from a Blu-ray disc and encoded to a lower resolution from its source (i.e. 1080p to 720p/576p/480p). BRRips are an already encoded video at HD resolution that is then transcoded to an SD resolution. BRRips are only from an HD resolution to a SD resolution whereas BDRips can go from 2160p to 1080p, etc as long as they go downward in resolution of the source disc. BDRips are not a transcode, but BRRips are. BD/BRRips in DVDRip resolutions can vary between XviD/x264/x265 codecs (commonly 700 MB and 1.5 GB in size as well as larger DVD5 or DVD9: 4.5 GB or 8.4GB). Size fluctuates depending on length and quality of releases, but the higher the size the more likely they use the x264/x265 codecs. A BD/BRRip to DVDRip resolution looks better, regardless, because the encode is from a higher quality source. BDRips and BRRips are often confused with each other, but have followed the above guideline after Blu-ray replaced BDRip title structure in scene releases.
Full BD25/BD50 data rips also exist, and are similar to their counterpart DVD5/DVD9 full data releases. They are AVCHD compatible using the BD folder structure (sometimes called Bluray RAW/m2ts/iso), and are usually intended to be burnt back to disk for play in AVCHD-compatible Blu-ray players. BD25/BD50 data rips may or may not be remuxed and are never transcoded.</li>
</ul></strong> </p>
<?php
$extra = '<ul class="custom_breadcrumb">
<li><a href="/">Home</a></li>
<li>Frequently Asked Questions</li>
</ul>';
Page::footer('',$extra);
?>