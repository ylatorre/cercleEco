    document.getElementById('scroll-left').addEventListener('click', function() {
        const container = document.getElementById('cards-container');
        container.scrollBy({
            left: -container.clientWidth,
            behavior: 'smooth'
        });
    });

    document.getElementById('scroll-right').addEventListener('click', function() {
        const container = document.getElementById('cards-container');
        container.scrollBy({
            left: container.clientWidth,
            behavior: 'smooth'
        });
    });
