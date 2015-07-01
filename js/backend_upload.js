<script type="text/javascript">
/* <![CDATA[ */

	function ActivateZipUpload(CB)
	{
		document.getElementById('filelist_{SECTION_ID}').style.display = (CB.checked == true) ? 'none' : 'table-header-group';
		var input = document.getElementById('file_script_{SECTION_ID}');
        var unzip = (CB.checked == true) ? '0' : '{MAX_UPLOADS}';
        input.setAttribute('maxlength',(unzip));
	}

   $(document).ready(function()
	{
		/**
       if($(".overlib").length)
	   {
         $.insert('{LEPTON_URL}/modules/modul_info/js/overlib.js');
	   }
	   **/
       if($("#upload_wrap_{SECTION_ID}").length)
	   {
			// $.insert('{LEPTON_URL}/modules/lib_jquery/jquery-ui/external/jquery.MultiFile.pack.js');
			// $.insert('{LEPTON_URL}/modules/lib_jquery/jquery-ui/external/jquery.MetaData.js');
			$('#upload_wrap_{SECTION_ID}').removeClass('hidden');
			$('.noscript').removeClass('hidden');
			$('#file_script_{SECTION_ID}').MultiFile({
				list: '#upload_wrap_{SECTION_ID}',
			 STRING: {
			  file: '<em title="Click to remove" onclick="$(this).parent().prev().click()">$file<\/em>',
			  remove: '<img src="{THEME_URL}/images/delete_16.png" height="16" width="16" alt="x"/>'
			 }
			});
       }
	});

/* ]]> */
</script>