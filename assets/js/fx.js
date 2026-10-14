/**
 * Background animation for Bungry Burger. Vanilla JS, one canvas, no dependencies.
 * Modes (Bungry Settings > Brand > Background animation): calm and crazy.
 * With prefers-reduced-motion the layer draws one still frame and does not animate.
 */
( function () {
	'use strict';

	var layer = document.querySelector( '.bungry-fx' );
	if ( ! layer ) {
		return;
	}
	var canvas = layer.querySelector( 'canvas' );
	var ctx = canvas && canvas.getContext ? canvas.getContext( '2d' ) : null;
	if ( ! ctx ) {
		return;
	}

	var crazy = 'crazy' === layer.getAttribute( 'data-mode' );
	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var COLORS = [ '#F6A531', '#98A75B', '#5E8441', '#E08F12', '#6D7A38', '#F6F7EE' ];
	var BLOB_COLORS = [ 'rgba(246,165,49,0.22)', 'rgba(152,167,91,0.22)', 'rgba(94,132,65,0.20)' ];
	var TAU = Math.PI * 2;

	var w = 0;
	var h = 0;
	var dpr = 1;
	var raf = 0;
	var last = 0;
	var particles = [];
	var blobs = [];
	var waves = [];
	var pointer = { x: -9999, y: -9999 };

	function rand( a, b ) {
		return a + Math.random() * ( b - a );
	}

	function makeParticle( x, y, burst ) {
		var roll = Math.random();
		var speed = crazy ? 1.6 : 0.6;
		return {
			x: x,
			y: y,
			vx: rand( -speed, speed ),
			vy: rand( -speed, speed ),
			r: rand( 2, crazy ? 9 : 6 ),
			rot: rand( 0, TAU ),
			vr: rand( -0.04, 0.04 ),
			color: COLORS[ ( Math.random() * COLORS.length ) | 0 ],
			shape: roll < 0.55 ? 'dot' : ( roll < 0.85 ? 'seed' : 'ring' ),
			burst: burst,
			life: 1
		};
	}

	function seed() {
		var area = w * h;
		var count = Math.min( crazy ? 160 : 50, Math.round( area / ( crazy ? 9000 : 16000 ) ) );
		var i;
		particles = [];
		for ( i = 0; i < count; i++ ) {
			particles.push( makeParticle( rand( 0, w ), rand( 0, h ), false ) );
		}

		blobs = [];
		var blobCount = crazy ? 5 : 3;
		for ( i = 0; i < blobCount; i++ ) {
			blobs.push( {
				x: rand( 0, w ),
				y: rand( 0, h ),
				r: rand( 220, 420 ),
				vx: rand( -0.15, 0.15 ),
				vy: rand( -0.15, 0.15 ),
				color: BLOB_COLORS[ i % BLOB_COLORS.length ]
			} );
		}

		waves = [];
		var waveCount = crazy ? 6 : 3;
		for ( i = 0; i < waveCount; i++ ) {
			waves.push( {
				y: rand( 0, h ),
				amp: rand( 18, 64 ),
				len: rand( 180, 420 ),
				speed: rand( 0.4, 1.4 ) * ( crazy ? 2.2 : 1 ),
				phase: rand( 0, TAU ),
				drift: rand( -0.12, 0.12 ),
				color: i % 2 ? '#98A75B' : '#F6A531'
			} );
		}
	}

	function resize() {
		dpr = Math.min( window.devicePixelRatio || 1, 2 );
		w = layer.clientWidth;
		h = layer.clientHeight;
		canvas.width = Math.round( w * dpr );
		canvas.height = Math.round( h * dpr );
		ctx.setTransform( dpr, 0, 0, dpr, 0, 0 );
		seed();
	}

	function wrap( value, min, max, pad ) {
		if ( value < min - pad ) {
			return max + pad;
		}
		if ( value > max + pad ) {
			return min - pad;
		}
		return value;
	}

	function drawBlobs( dt ) {
		var speed = crazy ? 2 : 1;
		for ( var i = 0; i < blobs.length; i++ ) {
			var b = blobs[ i ];
			b.x = wrap( b.x + b.vx * dt * speed, 0, w, b.r );
			b.y = wrap( b.y + b.vy * dt * speed, 0, h, b.r );
			var g = ctx.createRadialGradient( b.x, b.y, 0, b.x, b.y, b.r );
			g.addColorStop( 0, b.color );
			g.addColorStop( 1, 'rgba(0,0,0,0)' );
			ctx.fillStyle = g;
			ctx.fillRect( b.x - b.r, b.y - b.r, b.r * 2, b.r * 2 );
		}
	}

	function drawWaves( dt ) {
		ctx.lineWidth = crazy ? 3 : 2;
		ctx.globalAlpha = 0.35;
		for ( var i = 0; i < waves.length; i++ ) {
			var s = waves[ i ];
			s.phase += 0.02 * s.speed * dt;
			s.y = wrap( s.y + s.drift * dt, 0, h, s.amp );
			ctx.strokeStyle = s.color;
			ctx.beginPath();
			for ( var x = -20; x <= w + 20; x += 8 ) {
				var y = s.y + Math.sin( ( x / s.len ) * TAU + s.phase ) * s.amp;
				if ( x === -20 ) {
					ctx.moveTo( x, y );
				} else {
					ctx.lineTo( x, y );
				}
			}
			ctx.stroke();
		}
		ctx.globalAlpha = 1;
	}

	function drawParticles( dt ) {
		for ( var i = particles.length - 1; i >= 0; i-- ) {
			var p = particles[ i ];

			if ( crazy ) {
				// Mouse pushes particles away; friction keeps them from running off.
				var dx = p.x - pointer.x;
				var dy = p.y - pointer.y;
				var d2 = dx * dx + dy * dy;
				if ( d2 > 0 && d2 < 14400 ) {
					var d = Math.sqrt( d2 );
					var force = ( ( 120 - d ) / 120 ) * 0.9;
					p.vx += ( dx / d ) * force * dt;
					p.vy += ( dy / d ) * force * dt;
				}
				p.vx *= Math.pow( 0.985, dt );
				p.vy *= Math.pow( 0.985, dt );
			}

			p.x += p.vx * dt;
			p.y += p.vy * dt;
			p.rot += p.vr * dt;

			if ( p.burst ) {
				p.life -= 0.012 * dt;
				if ( p.life <= 0 ) {
					particles.splice( i, 1 );
					continue;
				}
			} else {
				p.x = wrap( p.x, 0, w, 20 );
				p.y = wrap( p.y, 0, h, 20 );
			}

			ctx.save();
			ctx.globalAlpha = p.burst ? Math.max( 0, p.life ) * 0.9 : 0.75;
			ctx.translate( p.x, p.y );
			ctx.rotate( p.rot );
			ctx.fillStyle = p.color;
			ctx.strokeStyle = p.color;
			ctx.lineWidth = 2;
			ctx.beginPath();
			if ( 'seed' === p.shape ) {
				ctx.ellipse( 0, 0, p.r * 1.3, p.r * 0.6, 0, 0, TAU );
				ctx.fill();
			} else if ( 'ring' === p.shape ) {
				ctx.arc( 0, 0, p.r * 1.2, 0, TAU );
				ctx.stroke();
			} else {
				ctx.arc( 0, 0, p.r, 0, TAU );
				ctx.fill();
			}
			ctx.restore();
		}
	}

	function draw( dt ) {
		ctx.clearRect( 0, 0, w, h );
		drawBlobs( dt );
		drawWaves( dt );
		drawParticles( dt );
	}

	function frame( t ) {
		var dt = last ? Math.min( 2.5, ( t - last ) / 16.67 ) : 1;
		last = t;
		draw( dt );
		raf = window.requestAnimationFrame( frame );
	}

	function start() {
		if ( ! raf && ! reduceMotion ) {
			last = 0;
			raf = window.requestAnimationFrame( frame );
		}
	}

	function stop() {
		if ( raf ) {
			window.cancelAnimationFrame( raf );
			raf = 0;
		}
	}

	var resizeFrame = 0;
	window.addEventListener( 'resize', function () {
		window.cancelAnimationFrame( resizeFrame );
		resizeFrame = window.requestAnimationFrame( function () {
			resize();
			if ( reduceMotion ) {
				draw( 0 );
			}
		} );
	} );

	document.addEventListener( 'visibilitychange', function () {
		if ( document.hidden ) {
			stop();
		} else {
			start();
		}
	} );

	if ( crazy ) {
		window.addEventListener( 'pointermove', function ( e ) {
			pointer.x = e.clientX;
			pointer.y = e.clientY;
		} );

		// Click or tap sprinkles seeds from the pointer. Form fields are left alone.
		window.addEventListener( 'pointerdown', function ( e ) {
			if ( e.target && e.target.closest && e.target.closest( 'input, textarea, select' ) ) {
				return;
			}
			for ( var i = 0; i < 22; i++ ) {
				var p = makeParticle( e.clientX, e.clientY, true );
				p.vx = rand( -4, 4 );
				p.vy = rand( -4, 4 );
				p.r = rand( 3, 10 );
				particles.push( p );
			}
		} );
	}

	resize();
	if ( reduceMotion ) {
		draw( 0 );
	} else {
		start();
	}
} )();
