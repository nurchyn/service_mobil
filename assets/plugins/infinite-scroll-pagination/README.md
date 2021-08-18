# Official post in
Github: https://github.com/vancevol/jquery-scroll-pagination/
Gitee: https://gitee.com/yiven/jquery-scroll-pagination/

# Example of usage:
```javascript
$(function(){
    $('#content').scrollPagination({
        'url': 'democontent.json', // The url you are fetching the results.
        'data': {
            // These are the variables you can pass to the request
            'page': 1, // Which page at the firsttime
            'size': 15, // Number of pages
        },
        'scroller': $(window), // Who gonna scroll? default is the full window
        'autoload':false, // Change this to false if you want to load manually, default true.
        'heightOffset': 0, // It gonna request when scroll is 10 pixels before the page ends
        'loading': "#loading", // ID of loading prompt.
        'loadingText': 'click to loading more.', // Text of loading prompt.
        'loadingNomoreText': 'No more.', // No more of loading prompt.
        'manuallyText': 'click to loading more.', // Click of loading prompt.
        'before': function(){  
            // Before load function, you can display a preloader div
            // example: 
            $(this.loading).fadeIn();
        },
        'after': function(elementsLoaded){
            // After loading content, you can use this function to animate your new elements
            // example:
            $(this.loading).fadeOut();
            $(elementsLoaded).fadeInWithDelay();
        }
    });
});
```

# Thanks
Thank you for using it!