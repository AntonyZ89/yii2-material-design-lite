'use strict';

{
    document.addEventListener("DOMContentLoaded", () => {
        const wrappers = document.getElementsByClassName('scroll__wrapper');

        for (let wrapper of wrappers) {
            const scroller = wrapper.getElementsByClassName('scroller')[0],
                container = wrapper.getElementsByClassName('scroll__container')[0],
                scrollbar = wrapper.getElementsByClassName('scroller__bar')[0];
            let scrollBarHeight;

            if (scroller) {
                let scrollWidth = scroller.offsetWidth - scroller.clientWidth;
                scrollWidth = scrollWidth < 7 ? 7 : scrollWidth;
                scroller.style.width = 'calc(100% + ' + scrollWidth + 'px)';

                scroller.addEventListener('scroll', () => {
                    if (scrollBarHeight > 8) {
                        scrollbar.style.top = 2 + scroller.scrollTop + 'px';
                    } else {
                        scrollbar.style.top = 2 + scroller.scrollTop * ((scroller.offsetHeight - 12) / (container.offsetHeight - scroller.offsetHeight)) + 'px'; //It's Math, yeah
                    }
                });

                function calcBarHeight() {
                    if (container.offsetHeight > scroller.offsetHeight) {
                        scrollBarHeight = scroller.offsetHeight - (container.offsetHeight - scroller.offsetHeight) - 4;
                        scrollbar.style.height = (scrollBarHeight > 8 ? scrollBarHeight : 8) + 'px';
                    } else {
                        scrollbar.style.height = 0;
                    }
                }

                wrapper.addEventListener('click', calcBarHeight);
                window.addEventListener('resize', calcBarHeight);

                calcBarHeight();
            }
        }
    });
}
