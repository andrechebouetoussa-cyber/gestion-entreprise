document.addEventListener('DOMContentLoaded', function(){
  function toggleMenu(){
    document.body.classList.toggle('menu-open');
  }

  document.querySelectorAll('#hamburger').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.stopPropagation();
      toggleMenu();
    });
  });

  // close when clicking outside
  document.addEventListener('click', function(e){
    if(document.body.classList.contains('menu-open')){
      if(!e.target.closest('.sidebar') && !e.target.closest('#hamburger')){
        document.body.classList.remove('menu-open');
      }
    }
  });

  // close on Escape
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') document.body.classList.remove('menu-open');
  });
});
