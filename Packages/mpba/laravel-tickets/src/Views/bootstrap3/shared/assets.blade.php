{{-- Load the css file to the header --}}
<script type="text/javascript">
    function loadCSS(filename) {
        var file = document.createElement("link");
        file.setAttribute("rel", "stylesheet");
        file.setAttribute("type", "text/css");
        file.setAttribute("href", filename);

        if (typeof file !== "undefined"){
            document.getElementsByTagName("head")[0].appendChild(file)
        }
    }
    loadCSS({!! '"'.asset('https://cdn.datatables.net/v/bs/dt-' . mpba\Tickets\Helpers\Cdn::DataTables . '/r-' . mpba\Tickets\Helpers\Cdn::DataTablesResponsive . '/datatables.min.css').'"' !!});
    @if($editor_enabled)
        loadCSS({!! '"'.asset('https://cdnjs.cloudflare.com/ajax/libs/summernote/' . mpba\Tickets\Helpers\Cdn::Summernote . '/summernote.css').'"' !!});
        @if($include_font_awesome)
            loadCSS({!! '"'.asset('https://netdna.bootstrapcdn.com/font-awesome/' . mpba\Tickets\Helpers\Cdn::FontAwesome . '/css/font-awesome.min.css').'"' !!});
        @endif
        @if($codemirror_enabled)
            loadCSS({!! '"'.asset('https://cdnjs.cloudflare.com/ajax/libs/codemirror/' . mpba\Tickets\Helpers\Cdn::CodeMirror . '/codemirror.min.css').'"' !!});
            loadCSS({!! '"'.asset('https://cdnjs.cloudflare.com/ajax/libs/codemirror/' . mpba\Tickets\Helpers\Cdn::CodeMirror . '/theme/'.$codemirror_theme.'.min.css').'"' !!});
        @endif
    @endif
</script>
