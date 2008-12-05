var hamstermas = {
    init: function() {
        var calendar = $('#christmas_calendar');
        var inner = $('#christmas_calendar_inner');
        var list = $('#christmas_tralist');
        
        var scroll = calendar.get(0).scrollWidth;
        
        hamstermas.calendar = calendar;
        hamstermas.calendar.scrollWidth = scroll - calendar.width();
        hamstermas.inner = inner;
        
        inner.width(scroll);
        list.width(scroll);
        
        hamstermas.scroll();
    },
    
    scroll: function() {
        var scroll = $('<div id="christmas_scroll"><div class="ui-slider-handle" id="christmas_handle"></div></div>').insertBefore(hamstermas.calendar);
        
        hamstermas.scroller = $('#christmas_scroll');
        hamstermas.handle   = $('#christmas_handle');
        
        // apply styles
        hamstermas.scroller.css({
           width: '100%',
           height: 20,
           background: '#f2d2e1',
           position: 'relative'
        });
        
        hamstermas.handle.css({
            width: 50,
            height: 20,
            position: 'absolute',
            background: '#3d3dff'
        });
        
        hamstermas.scroller.slider({
            animate: true,
            slide: function(e, ui) {
                var percent = ui.value / 100;
                hamstermas.calendar.scrollLeft(hamstermas.calendar.scrollWidth * percent);
            },
            steps: 5000
        });
    },
    
    include_script: function(src, callback) {
        var js = $('<sc' + 'ript />');
        js.load(callback);
        js.attr('src', src);
        js.attr('type', 'text/javascript');
        document.body.appendChild(js.get(0));
    }
};

hamstermas.include_script('/javascripts/jquery-ui-slider.js', function() {
    hamstermas.init();
});