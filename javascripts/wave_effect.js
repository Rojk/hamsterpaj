function randomInt(start, end) {
    return Math.floor(Math.random()*(end+1-start)+start)
}

var maxX, minX, maxY, minY;
maxX = 1;
minX = -1;
maxY = 1;
minY = -1;

function kakelugn(element_,avoidCursor_) {

    this.element = element_;
    this.avoidCursor = avoidCursor_?avoidCursor_:false;
    this.brinner = false;
    this.velX = randomInt(-3,3);
    this.velY = randomInt(-3,3);
    
    this.posX = 0;
    this.posY = 0;
    
    this.sizeX = 0;
    this.sizeY = 0;
    
    this.taend = function() {
        this.sizeX = parseInt(this.element.style.offsetWidth)?parseInt(this.element.style.offsetWidth):200;
        this.sizeY = parseInt(this.element.style.offsetHeight)?parseInt(this.element.style.offsetHeight):200;
        this.element.style.position="relative";
        this.brinner = true;
    }
    this.Update = function() {
        this.posX += this.velX;
        this.posY += this.velY;
        
        if(this.posX<minX) {
            this.posX = minX;
            this.velX = -this.velX;
        }
        if(this.posY<minY) {
            this.posY =minY;
            this.velY = -this.velY;
        }
        if(this.posX>maxX) {
            this.posX = maxX;
            this.velX *= -1;
        }
        if(this.posY>maxY) {
            this.posY = maxY;
            this.velY *= -1;
        }    
    }
    this.Draw = function() {
        if(!this.brinner) this.taend();
        this.element.style.top=this.posY+"px";
        this.element.style.left=this.posX+"px";
    }

}
var running = false;
var ugnar = new Array();

function stop_start_wave_effect() {

    var taggar =  new Array("div","p","a","img");
    var n = 0;
    for(j in taggar) {
    
        var elements = document.all ?  document.all.tags(taggar[j]) : document.getElementsByTagName(taggar[j])

        for(var i = 0;i < elements.length; i++) {
            if(
            (elements[i].className=="module_container_open")||
            (elements[i].id=="main")||
            (elements[i].id=="top")
            )
                //elements[i].style.display="none";
                elements[i].style.zIndex="1";
            else if(elements[i].id=="content")
                elements[i].style.zIndex="2";
            else {
                elements[i].style.zIndex="100";
                
                ugnar[n] = new kakelugn(elements[i],false);
                n++;
            }
        }
    }
    if(randomInt(1,6)==2)
        kakelBlock(50);
    else
        kakelBlock(randomInt(4,15));
    
    


    if(!running) {
        setTimeout("stop_start_wave_effect_Loop()",500);
        running = true;
    }
    
}
function kakelBlock(antal) {
    maxX = antal;
    minX = -antal;
    maxY = antal;
    minY = -antal;
}
function stop_start_wave_effect_Loop() {

    for(ugn in ugnar) {
        ugnar[ugn].Update();
        ugnar[ugn].Draw();
        
    }
    setTimeout("stop_start_wave_effect_Loop()",50);
}