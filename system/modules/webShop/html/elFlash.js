Element.implement({
  flash: function(to,from,reps,prop,dur) {
    
    //defaults
    if(!reps) { reps = 1; }
    if(!prop) { prop = 'background-color'; }
    if(!dur) { dur = 250; }
    
    //create effect
    var effect = new Fx.Tween(this, {
        duration: dur,
        link: 'chain'
      })
    
    //do it!
    for(x = 1; x <= reps; x++)
    {
      effect.start(prop,from,to).start(prop,to,from);
    }
  }
});
