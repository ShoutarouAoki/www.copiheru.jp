/**************************************************************
**
**	mail.webgl.js
**	----------------------------------------------------------
**	ORIGNAL JAVASCRIPT FILE
**	----------------------------------------------------------
**	メール用 WEBGLコントロール関数群
**	
**
**************************************************************/

/************************************
**
**	javascript内の変数定義（WEBGLコントロール用）
**
************************************/
//ゲームオブジェクト
let game;

//パーティクルに使うアニメの元データ
let anim;
let anim_sakura;

//エミッタ―
let left_emit = [4];
let bottom_emit = [8];

//初期化済みフラグ
let inited=0;

//マニーマックス
let maniimax_setted_flg = 0;
let maniimax_move_flg = 0;
const maniimax_particle_num = 80;
const maniimax_effect_millitime = 4000;
var maniimax_sprites = [];
//var maniimax_bg_graphics;

/************************************
**
**	パーティクルに使うアニメクラス
**
************************************/
class AnimatedParticle extends Phaser.GameObjects.Particles.Particle
{
    constructor (emitter)
    {
        super(emitter);

        this.t = 0;
        this.i = 0;
    }

    update (delta, step, processors)
    {
        super.update(delta, step, processors);

        this.t += delta;

        if (this.t >= anim.msPerFrame)
        {
            this.i++;

            if (this.i > 11)
            {
                this.i = 0;
            }

            this.frame = anim.frames[this.i].frame;

            this.t -= anim.msPerFrame;
        }
    }
}

class AnimatedParticle_sakura extends Phaser.GameObjects.Particles.Particle
{
    constructor (emitter)
    {
        super(emitter);

        this.t = 0;
        this.i = 0;
    }

    update (delta, step, processors)
    {
        super.update(delta, step, processors);

        this.t += delta;

        if (this.t >= anim_sakura.msPerFrame)
        {
            this.i++;

            if (this.i > 3)
            {
                this.i = 0;
            }

            this.frame = anim_sakura.frames[this.i].frame;

            this.t -= anim_sakura.msPerFrame;
        }
    }
}
/************************************
**
**	起動時
**
************************************/
function preload ()
{
    this.load.spritesheet('particle', '/images/smart/maniis.png', { frameWidth: 100, frameHeight: 100 });
    this.load.spritesheet('particle_sakura', '/images/smart/sakura_particles.png', { frameWidth: 32, frameHeight: 32 });
    this.load.image('particle_maniimax', '/images/smart/heart.png');
    //this.load.image('bg', '/images/smart/space1.png');
}

/************************************
**
**	画面作成
**
************************************/
function create ()
{
    //1
    let config_anime = {
        key: 'walk',
        frames: this.anims.generateFrameNumbers('particle'),
        frameRate: 12,
        repeat: -1
    };

    anim = this.anims.create(config_anime);
    
    //2
    let config_anime_sakura = {
        key: 'sakura',
        frames: this.anims.generateFrameNumbers('particle_sakura'),
        frameRate: 4,
        repeat: -1
    };
    
    anim_sakura = this.anims.create(config_anime_sakura);
    
    let game_screen_width = $("#"+valParentId).outerWidth(true);
    let game_screen_height = $("#"+valParentId).outerHeight(true);
    
    game.resize(game_screen_width,game_screen_height);

    //1
    let particles = this.add.particles('particle');
    let bottom_emit_zone = new Phaser.Geom.Rectangle(0, game_screen_height/2, game_screen_width, game_screen_height);
    
    //2
    let particles_sakura = this.add.particles('particle_sakura');
    let left_emit_zone = new Phaser.Geom.Rectangle(-game_screen_width/5, 0, 0, game_screen_height);

    for(index=0;index<4;index++){
        left_emit[index] = particles_sakura.createEmitter({
            //accelerationX:600,
            //accelerationY:600,
            active:false,
            alpha: valAlpha_sakura,
            angle: { min: 330, max: 360 },
            blendMode: 'ADD',
            bounce:1,
            //bounds:{ x: 250, y: 200, w: 350, h: 200 },
            //collideTop:false, collideBottom:false, collideLeft:false, collideRight:false, 
            //currentFrame:0,
            //deathCallback: null
            //deathCallbackScope: null,
            //deathZone: null,
            delay: 0,
            //emitCallback: null
            //emitCallbackScope: null,
            emitZone:{source: left_emit_zone},
            //follow: null,
            //followOffset:null,
            //frameQuantity: 1, 
            frequency: valFrequency_sakura,
            frame: index,
            //gravityX:0,
            gravityY: valGravityY_sakura,
            lifespan: valLifeSpan_sakura,
            //maxParticles: valMaxparticle,
            //maxVelocityX: 0,
            //maxVelocityY: 0,
            //moveToX: 0,
            //moveToY: 0,
    //            name: "left"+index,
            //on: true,
            quantity: valQuantity_sakura,
            //particleBringToTop:true,
            //radial: true,
            //randomFrame:true,
            rotate:{ min: 0, max: 360 },
            scale: valScale_sakura,
            //scaleX:1,
            //scaleY:1,
            //speed: valSpeed,
            speedX:valSpeedX_sakura,
            speedY:valSpeedY_sakura,
            //timeScale:1,
            tint:0xffffff,
            visible:true,
            //x: game_screen_width/2,
            //y: -game_screen_height/12,
            particleClass: AnimatedParticle_sakura
        });
    }
    
    for(index=0;index<8;index++){
        bottom_emit[index] = particles.createEmitter({
            //accelerationX:600,
            //accelerationY:600,
            active:false,
            alpha: valAlpha_bottom,
            angle: { min: 265, max: 275 },
            blendMode: 'ADD',
            bounce:1,
            //bounds:{ x: 250, y: 200, w: 350, h: 200 },
            //collideTop:false, collideBottom:false, collideLeft:false, collideRight:false, 
            //currentFrame:0,
            //deathCallback: null
            //deathCallbackScope: null,
            //deathZone: null,
            delay: 0,
            //emitCallback: null
            //emitCallbackScope: null,
            emitZone:{source: bottom_emit_zone},
            //follow: null,
            //followOffset:null,
            //frameQuantity: 1, 
            frequency: valFrequency_bottom,
            frame: index,
            gravityX:0,
            gravityY: valGravityY_bottom,
            lifespan: valLifeSpan_bottom,
            //maxParticles: 100,
            //maxVelocityX: 0,
            //maxVelocityY: 0,
            //moveToX: 0,
            //moveToY: 0,
    //            name: "left"+index,
            //on: true,
            quantity: valQuantity_bottom,
            //particleBringToTop:true,
            //radial: true,
            //randomFrame:true,
            //rotate:{ min: 20, max: 60 },
            scale: valScale_bottom,
            //scaleX:1,
            //scaleY:1,
            speedY: valSpeedY_bottom,
            //speedX:1
            //speedY:1,
            //timeScale:1,
            tint:0xffffff,
            visible:true,
            //x: game_screen_width/2,
            //y: -game_screen_height/12,
        });
    }

//    maniimax_bg_graphics = this.add.graphics();
//    maniimax_bg_graphics.setBlendMode(Phaser.BlendModes.SCREEN);
//    this.add.image(400, 300, 'bg').setBlendMode(Phaser.BlendModes.SCREEN);;
    for (let i = 0; i < maniimax_particle_num; i++){
        let x = Phaser.Math.Between(-64, game_screen_width);
        let y = Phaser.Math.Between(-64, game_screen_height);
        
        var image = this.add.image(x, y, 'particle_maniimax');
        image.setVisible(false);

        //  Canvas and WebGL:
        image.setBlendMode(Phaser.BlendModes.NORMAL);

        maniimax_sprites.push({ heart: image, movey: 2 + Math.random() * 6 });
    }
    
    maniimax_setted_flg = 1;
    
}


/************************************
**
**	画面更新
**
************************************/
function update ()
{
    updateManiiMaxEffect();
}

/************************************
**
**	マニーMAX時のエフェクト
**
************************************/
function setManiiMaxEffect(){
    //  Create the particles
    /*
    this.add.image(400, 300, 'particle_maniimax');
    for (let i = 0; i < maniimax_particle_num; i++){
        let x = Phaser.Math.Between(-64, 800);
        let y = Phaser.Math.Between(-64, 600);

        let image = this.add.image(x, y, 'particle_maniimax');

        //  Canvas and WebGL:
        image.setBlendMode(Phaser.BlendModes.ADD);

        sprites.push({ heart: image, movey: 2 + Math.random() * 6 });
    }

    maniimax_setted_flg = 1;
    */
}

function startManiiMaxEffect()
{
    if(maniimax_setted_flg){
        let game_screen_width = $("#"+valParentId).outerWidth(true);
        let game_screen_height = $("#"+valParentId).outerHeight(true);
//        maniimax_bg_graphics.fillStyle(0x660066, 0.6);
//        maniimax_bg_graphics.fillRect(0, 0, game_screen_width, game_screen_height);
        
        for (let i = 0; i < maniimax_particle_num; i++){
            let sprite = maniimax_sprites[i].heart;
            sprite.active=true;
            sprite.setVisible(true);
        }
        maniimax_move_flg = 1;
        $("#"+valParentId).css("z-index",9999);
        $("#"+valParentId).css("opacity",1);
        setTimeout(finishManiiMaxEffect, maniimax_effect_millitime);
    }
}

function updateManiiMaxEffect()
{
    if(maniimax_setted_flg && maniimax_move_flg){
        for (let i = 0; i < maniimax_sprites.length; i++){
            let sprite = maniimax_sprites[i].heart;
            sprite.y -= maniimax_sprites[i].movey;

            let game_screen_height = $("#"+valParentId).outerHeight(true);
            if (sprite.y < -game_screen_height/4){
                sprite.y = 5*game_screen_height/4;
            }
        }
    }
}

function finishManiiMaxEffect(){
    if(maniimax_setted_flg && maniimax_move_flg){
        $("#"+valParentId).animate({ opacity: 0 }, 500, "swing",  function(){
            $("#"+valParentId).css("z-index",0);
            for (let i = 0; i < maniimax_particle_num; i++){
                let sprite = maniimax_sprites[i].heart;
                sprite.active=false;
                sprite.setVisible(false);
            }
            maniimax_move_flg=0;
//            maniimax_bg_graphics.clear();
        });
    }
}

/************************************
**
**	ページ本体からのイベント発火関数
**
************************************/
function gameStart(){
    if(!inited){
        let config = {
            title: valTitle,
            version: valVersion,
            banner: {
                text: "#000",
                background: ["#ff0000", "#00ff00", "#0000ff", "#ffff00"],
                hidePhaser: true
            },
            type: Phaser.AUTO,
            parent :valParentId,
            width: $("#"+valParentId).outerWidth(true),
            height: $("#"+valParentId).outerHeight(true),
            physics: {
                default: 'arcade',
                arcade: {
                    gravity: { y: 300 },
                    debug: false
                }
            },
            scene: {
                preload: preload,
                create: create,
                update: update
            },
            transparent: true,
        };

        game = new Phaser.Game(config);
        
        inited = 1;

        $("#"+valParentId).on('touchend mouseup', function(){
            $("#"+valParentId).animate({ opacity: 0 }, 500, "swing",  function(){
                $("#"+valParentId).css("z-index",0);
                for(index=0;index<8;index++){
                    bottom_emit[index].pause();
                    bottom_emit[index].killAll();
                }
                for(index=0;index<4;index++){
                    left_emit[index].pause();
                    left_emit[index].killAll();
                }
            })
        })
    }
}

function startParticles() {
    $("#"+valParentId).css("z-index",9999);
    $("#"+valParentId).css("opacity",1);
    for(index=0;index<8;index++){
        bottom_emit[index].resume();
    }
    setTimeout(endParticles, valEffectMillitime);
}

function endParticles() {
    $("#"+valParentId).animate({ opacity: 0 }, 500, "swing",  function(){
        $("#"+valParentId).css("z-index",0);
        for(index=0;index<8;index++){
            bottom_emit[index].pause();
            bottom_emit[index].killAll();
        }
        if(manii_over_animation)
            startManiiMaxEffect();
    });
}

function startParticles_Sakura() {
    $("#"+valParentId).css("z-index",9999);
    $("#"+valParentId).css("opacity",1);
    for(index=0;index<4;index++){
        left_emit[index].resume();
    }
    setTimeout(endParticles_Sakura, valEffectMillitime_sakura);
}

function endParticles_Sakura() {
    $("#"+valParentId).animate({ opacity: 0 }, 500, "swing",  function(){
        $("#"+valParentId).css("z-index",0);
        for(index=0;index<4;index++){
            left_emit[index].pause();
            left_emit[index].killAll();
        }
    });
}