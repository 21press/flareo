/**
 * Extract flare configuration from given arguments.
 * 
 * @param {Object} args - The arguments containing flare configuration.
 * @returns {Object} - Extracted flare configuration.
 */
function p21GetFlareoFlareConfig( args = {} ) {

	return Object.keys(args).filter(function(k) {
						return k.indexOf('flare') == 0;
				}).reduce(function(newConfig, k) {
						newConfig[k] = args[k];
						return newConfig;
				}, {});
}

function p21FlareoFlarePresetSideCannon( args = {} ) {
	const end = Date.now() + 0.1 * 1000; // 0.1 second
	const frame = () => {
		if (Date.now() > end) return
		confetti({
			particleCount: 6,
			angle: 60,
			spread: 55,
			startVelocity: 60,
			origin: { x: 0, y: 0.5 },
			...args,
		})
		confetti({
			particleCount: 6,
			angle: 120,
			spread: 55,
			startVelocity: 60,
			origin: { x: 1, y: 0.5 },
			...args,
		})
		requestAnimationFrame(frame);
	};
	frame();
}

function p21FlareoFlarePresetBottomCannon( args = {} ) {
	const end = Date.now() + 0.1 * 1000; // 0.1 second
	const frame = () => {
		if (Date.now() > end) return
		confetti({
			particleCount: 20,
			angle: 60,
			spread: 55,
			startVelocity: 100,
			origin: { x: 0, y: 1 },
			...args,
		})
		confetti({
			particleCount: 20,
			angle: 120,
			spread: 55,
			startVelocity: 100,
			origin: { x: 1, y: 1 },
			...args,
		})
		requestAnimationFrame(frame);
	};
	frame();
}

function p21FlareoFlarePresetCenterFountain( args = {} ) {
	const end = Date.now() + 3 * 1000 // 3 seconds
	const frame = () => {
		if (Date.now() > end) return
		confetti({
			particleCount: 8,
			spread: 55,
			startVelocity: 60,
			...args,
		})
		requestAnimationFrame(frame)
	}
	frame();
}

function p21FlareoFlarePresetSideFountain( args = {} ) {
	const end = Date.now() + 3 * 1000; // 3 seconds
	const frame = () => {
		if (Date.now() > end) return
		confetti({
			particleCount: 8,
			angle: 60,
			spread: 55,
			startVelocity: 60,
			origin: { x: 0, y: 0.5 },
			...args,
		})
		confetti({
			particleCount: 8,
			angle: 120,
			spread: 55,
			startVelocity: 60,
			origin: { x: 1, y: 0.5 },
			...args,
		})
		requestAnimationFrame(frame);
	}
	frame();
}

function p21FlareoFlarePresetBottomFountain( args = {} ) {
	const end = Date.now() + 3 * 1000; // 3 seconds
	const frame = () => {
		if (Date.now() > end) return
		confetti({
			particleCount: 7,
			angle: 60,
			spread: 55,
			startVelocity: 100,
			origin: { x: 0, y: 1 },
			...args,
		})
		confetti({
			particleCount: 7,
			angle: 120,
			spread: 55,
			startVelocity: 100,
			origin: { x: 1, y: 1 },
			...args,
		})
		requestAnimationFrame(frame);
	}
	frame();
}

function p21FlareoFlarePresetFireworks( args = {} ) {
	const duration = 5 * 1000;
	const animationEnd = Date.now() + duration;
	const defaultArgs = { startVelocity: 30, spread: 360, ticks: 60 };
	const randomInRange = (min, max) =>
		Math.random() * (max - min) + min
	const interval = window.setInterval(() => {
		const timeLeft = animationEnd - Date.now()
		if (timeLeft <= 0) {
			return clearInterval(interval)
		}
		const particleCount = 50 * (timeLeft / duration)
		confetti({
			...defaultArgs,
			particleCount,
			origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 },
			...args,
		})
		confetti({
			...defaultArgs,
			particleCount,
			origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 },
			...args,
		})
	}, 250);
}

function p21FlareoFlarePresetBurst( args = {} ) {
	const defaults = {
      spread: 360,
      ticks: 50,
      gravity: 0,
      decay: 0.94,
      startVelocity: 30,
    }
    const shoot = () => {
      confetti({
        ...defaults,
        particleCount: 40,
				...args,
      });
      confetti({
        ...defaults,
        particleCount: 10,
			  ...args,
      });
    }
    setTimeout(shoot, 0);
    setTimeout(shoot, 100);
    setTimeout(shoot, 200);
}

function p21FlareoFlarePresetFalling( args = {} ) {
	const duration = 15 * 1000;
	const animationEnd = Date.now() + duration;
	let skew = 1;

	function randomInRange(min, max) {
		return Math.random() * (max - min) + min;
	}

	function frame() {
		const timeLeft = animationEnd - Date.now();
		const ticks = Math.max(200, 500 * (timeLeft / duration));
		skew = Math.max(0.8, skew - 0.001);

		confetti({
			particleCount: 1,
			startVelocity: 0,
			ticks: ticks,
			origin: {
				x: Math.random(),
				// since particles fall down, skew start toward the top
				y: (Math.random() * skew) - 0.2
			},
			gravity: randomInRange(0.4, 0.6),
			drift: randomInRange(-0.4, 0.4),
			...args,
		});

		if (timeLeft > 0) {
			requestAnimationFrame(frame);
		}
	}

	frame();
}

function p21FlareoFlarePresetMeteorShower(args = {}) {
	let meteorCount = 0;
	const totalMeteors = 30;

	function shootMeteor(args = {}) {
		confetti({
			particleCount: 1,
			startVelocity: 2,
			// ticks: 2000,
			origin: {
				x: Math.random() * 0.5 + 0.5, // Top Right (randomized slightly)
				y: Math.random() - 0.2        // Slightly above top edge
			},
			gravity: 0.6,
			drift: -1.8,
			angle: 225,
			flat: true, // Stop spinning
			...args,
		});

		meteorCount++;
	}

	// 1. Shoot the first one immediately
	shootMeteor(args);

	// 2. Set up the interval for the rest
	const showerInterval = setInterval(() => {
		if (meteorCount >= totalMeteors) {
			clearInterval(showerInterval); // Stop after 5 meteors
			return;
		}
		
		shootMeteor({
			...args,
			decay: Math.random() * 0.5 + 0.5, // Vary decay for each meteor
			ticks: Math.random() * 1000 + 1000 // Vary ticks for each meteor
		});
	}, 500); // 1000ms = 1 second delay
}

/**
 * Run flare preset configuration.
 * 
 * @param {Object} args - Configuration options for the flare effect.
 */
async function p21FlareoFlareRunPreset( args = {}, presetStyle = 'basic' ) {
	switch ( presetStyle ) {
		case 'center-cannon':
			await confetti({
					...args,
					particleCount: 100,
				});
			break;
		case 'side-cannon':
			p21FlareoFlarePresetSideCannon( args );
			break;
		case 'bottom-cannon':
			p21FlareoFlarePresetBottomCannon( args );
			break;
		case 'center-fountain':
			p21FlareoFlarePresetCenterFountain( args );
			break;
		case 'side-fountain':
			p21FlareoFlarePresetSideFountain( args );
			break;
		case 'bottom-fountain':
			p21FlareoFlarePresetBottomFountain( args );
			break;
		case 'random-direction':
			await confetti({
				...args,
				angle: Math.random() * 360,
				particleCount: 100,
			});
			break;
		case 'fireworks':
			p21FlareoFlarePresetFireworks( args );
			break;
		case 'falling':
			p21FlareoFlarePresetFalling( args );
			break;
		case 'burst':
			p21FlareoFlarePresetBurst( args );
			break;
		case 'meteor-shower':
			p21FlareoFlarePresetMeteorShower( args );
			break;
		default:
			break;
	}
}

async function p21FlareoFlarePresetShape( presetType = 'stars', args = {}, colors = [] ) {
	let shapes = [];
	switch ( presetType ) {
		case 'stars':
			args.colors = [ ...colors ];
			args.shapes = [ 'star' ];
			break;
		case 'hearts':
			const heartShape = confetti.shapeFromPath(confetti.shapeFromPath({
						path: 'm522.37 1107.5c-168.1-131.58-299.76-269.95-384.28-402.61-84.609-132.79-122.39-260.63-103.12-371.39 12.75-73.312 45-138.98 91.875-186.94 29.766-30.516 65.625-53.906 106.27-67.547 40.547-13.641 85.594-17.531 133.82-9.1406 71.859 12.516 151.03 52.312 233.02 128.02 82.031-75.703 161.16-115.5 233.02-128.02 48.234-8.3906 93.281-4.5 133.82 9.1406 40.641 13.688 76.547 37.031 106.36 67.5l1.3125 1.4531c46.125 47.812 77.859 112.92 90.516 185.48 19.312 110.77-18.516 238.55-103.08 371.34-84.516 132.66-216.24 271.08-384.32 402.66-23.062 18.094-50.391 27.094-77.578 27.094-27.234 0-54.516-9.0469-77.578-27.094zm-235.08-949.64c-38.672 6.6094-70.828 25.688-96.234 51.891-28.359 29.203-48.328 67.359-59.812 106.92-2.7656 9.5156 2.7188 19.453 12.234 22.219s19.453-2.7188 22.219-12.234c9.9375-34.266 27.047-67.078 51.094-91.875 20.344-20.953 45.984-36.234 76.547-41.484 9.7969-1.6406 16.359-10.969 14.719-20.719-1.6406-9.7969-10.969-16.359-20.719-14.719zm-167.06 243 0.70312 13.312c0.60938 9.8906 9.1406 17.438 19.031 16.828s17.438-9.1406 16.828-19.031l-0.70312-12.516c-0.375-9.8906-8.7656-17.625-18.656-17.203-9.8906 0.375-17.625 8.7656-17.203 18.656z',
						matrix: [0.0083333333, 0, 0, 0.0083333333, -5, -5],
					}));
			args.scalar = 2;
			args.colors = [ ...colors ];
			args.shapes = [ heartShape ];
			break;
		case 'snow':
			const snowShape = confetti.shapeFromPath({
					path: 'm1055.5 843.19-95.906-58.125 84.516-20.766c15.094-3.7031 24.328-18.938 20.625-34.031s-18.938-24.328-34.031-20.625l-139.31 34.172-139.78-84.938v-117.75l139.97-84.75 139.22 34.266v0.046875c2.2031 0.51562 4.4531 0.79688 6.75 0.79688 14.203 0 26.203-10.641 27.891-24.75s-7.4062-27.281-21.188-30.703l-84.656-20.812 96.141-58.172c13.266-8.0625 17.531-25.359 9.4688-38.625-8.0156-13.312-25.312-17.531-38.625-9.5156l-96.047 58.125 20.812-84.516c1.8281-7.2188 0.65625-14.906-3.1875-21.328s-10.125-11.016-17.391-12.797-14.906-0.60938-21.328 3.2812c-6.375 3.8906-10.969 10.172-12.703 17.438l-34.219 139.18-141.47 85.641-92.953-48.281v-169.5l101.34-101.34c11.016-10.969 11.016-28.781 0.046875-39.797-11.016-10.969-28.828-10.969-39.797 0l-61.594 61.641v-112.27c0-15.516-12.609-28.125-28.125-28.125s-28.125 12.609-28.125 28.125v112.27l-61.594-61.641c-10.969-10.969-28.781-10.969-39.797 0-10.969 11.016-10.969 28.828 0.046875 39.797l101.34 101.34v169.5l-92.906 48.234-141.37-85.875-34.125-139.18 0.046875 0.046875c-1.7812-7.2656-6.375-13.547-12.75-17.391-6.375-3.8906-14.062-5.0625-21.281-3.2812-7.2656 1.7812-13.5 6.375-17.391 12.75-3.8438 6.375-5.0156 14.062-3.2344 21.281l20.766 84.656-96.047-58.219 0.046875 0.046875c-13.312-8.0625-30.609-3.7969-38.672 9.4688-8.0156 13.312-3.7969 30.609 9.5156 38.625l95.906 58.125-84.516 20.766c-15.094 3.7031-24.328 18.938-20.625 34.031s18.938 24.328 34.031 20.625l139.31-34.172 139.78 84.938v117.75l-139.97 84.75-139.22-34.266v-0.046875c-7.2656-1.7812-14.906-0.65625-21.328 3.2344-6.4219 3.8438-11.016 10.078-12.797 17.344s-0.60938 14.953 3.2812 21.328 10.125 10.969 17.391 12.75l84.656 20.812-96.141 58.172c-6.375 3.8906-10.969 10.125-12.75 17.344-1.7812 7.2656-0.60938 14.906 3.2812 21.281 8.0156 13.312 25.312 17.531 38.625 9.5156l96.047-58.125-20.812 84.516c-3.7031 15.047 5.4844 30.281 20.578 34.031 2.2031 0.51562 4.4531 0.79688 6.75 0.79688 12.938 0 24.188-8.8594 27.281-21.422l34.219-139.18 141.47-85.641 92.953 48.281v169.5l-101.34 101.34c-11.016 10.969-11.016 28.781-0.046875 39.797 11.016 10.969 28.828 10.969 39.797 0l61.594-61.641v112.27c0 15.516 12.609 28.125 28.125 28.125s28.125-12.609 28.125-28.125v-112.27l61.594 61.594v0.046875c10.969 10.969 28.781 10.969 39.797 0 10.969-11.016 10.969-28.828-0.046875-39.797l-101.34-101.34v-169.5l92.906-48.234 141.37 85.875 34.125 139.18-0.046875-0.046875c3.75 15.094 18.938 24.281 34.031 20.578 15.094-3.6562 24.281-18.891 20.625-33.938l-20.766-84.656 96.047 58.219-0.046875-0.046875c13.312 8.0625 30.609 3.7969 38.672-9.4688 8.0156-13.312 3.7969-30.609-9.5156-38.625z',
					matrix: [0.0083333333, 0, 0, 0.0083333333, -5, -5]
				});

			args.scalar = 2;
			args.flat = true;
			args.colors = [ ...colors ];
			args.shapes = [ snowShape ];
			break;
		case 'lightning':
			const lightningShape = confetti.shapeFromPath({
						path: 'm540.46 143.33-178.42 468.07c-3.4688 9.1094 3.2539 18.863 12.996 18.863h118.4c9.1445 0 15.805 8.6758 13.441 17.508l-107.21 400.26c-3.8984 14.566 14.93 24.059 24.324 12.266l411.83-517.37c7.0312-8.832 1.1758-21.91-10.105-22.559l-188.03-10.633c-9.7422-0.55078-15.91-10.703-11.902-19.609l151.44-336.14c4.1523-9.2031-2.5938-19.633-12.684-19.633h-211.08c-5.7734 0.023438-10.945 3.5898-13.008 8.9766z',
						matrix: [0.0083333333, 0, 0, 0.0083333333, -5, -5]
					});
			args.scalar = 4;
			args.colors = [ ...colors ];
			args.shapes = [ lightningShape ];
			break;
		case 'sparkles':
			const sparkleShape = confetti.shapeFromPath({
						path: 'm88.801 1122.2c-12.719-12.719-40.078-25.32-66.84-35.641 28.078-10.32 57-23.398 70.32-36.84 6-6.1211 11.762-15.48 17.398-26.52 5.5195-11.039 10.68-23.762 15.359-36.48 9.3594 25.559 20.762 50.879 32.762 63 13.199 13.441 42.121 26.398 70.199 36.84-26.879 10.32-54.121 22.922-66.84 35.641-12.961 12.961-25.801 40.922-36.121 68.16-10.438-27.238-23.277-55.199-36.238-68.16zm344.16-654c-66.723 67.562-282 138.72-396.96 173.16 112.2 35.281 319.68 106.8 384.84 172.08 65.641 65.641 137.52 274.8 172.56 386.64 35.039-111.84 106.92-321 172.56-386.64 65.281-65.281 272.64-136.8 384.84-172.08-115.08-34.441-330.36-105.6-396.96-173.16-63.238-64.078-127.8-265.68-160.44-379.2-32.633 113.52-97.195 315-160.43 379.2zm636.36-368.52c-18-18.238-35.281-60.84-48.359-99.719-12.961 38.879-30.359 81.48-48.359 99.719-19.559 19.922-67.078 39.359-108.72 53.52 39.961 14.281 85.199 33.359 104.16 52.32 19.199 19.199 38.52 65.281 52.922 105.6 14.281-40.32 33.602-86.398 52.922-105.6 18.961-18.961 64.199-38.16 104.16-52.32-41.527-14.16-89.047-33.598-108.73-53.52z',
						matrix: [0.0083333333, 0, 0, 0.0083333333, -5, -5]
					});
			args.scalar = 2;
			args.colors = [ ...colors ];
			args.flat = true;
			args.shapes = [ sparkleShape ];
			break;
		case 'meteor':
			const longMeteor = confetti.shapeFromPath({
				path: 'M54 1534.87L1375.87 213L1389 226.13L67.1298 1548L54 1534.87Z M1447 140.711L1533.97 52L1547 65.2894L1460.03 154L1447 140.711Z',
				matrix: [0.00625, 0, 0, 0.00625, -5, -5],
			  scalar: 50,
			});

			args.scalar = 5;
			args.flat = true;
			args.colors = [ ...colors ];
			args.shapes = [ longMeteor ];
			break;
		default:
			args.colors = [ ...colors ];
			args.shapes = [ 'star' ];
			break;
	}
	return args;
}

/**
 * Trigger a flare effect with given arguments.
 * 
 * @param {Object} args - Configuration options for the flare effect.
 */
async function p21FlareoFlareTrigger( args = {} ) {
		// Default args for flare.
		const default_args = {
			flareType: 'preset',
			flarePresetType: 'confetti',
			flarePresetStyle: 'center-cannon',
			zIndex: 9999,
		};

		// Prioritize passed args over default args.
		args = { ...default_args, ...args };

		// Extract flare configuration from args.
		const flareConfig = p21GetFlareoFlareConfig( args );

		// Presets way.
		if ( flareConfig && Object.keys(flareConfig).length > 0 && flareConfig.flareType === 'preset' ) {
			const presetType = flareConfig.flarePresetType || 'confetti';
			const presetStyle = flareConfig.flarePresetStyle || 'center-cannon';
			const globalDefaultColor = flareConfig.flareGlobalColor || '#6B4DEC';

			if ( presetType && 'confetti' !== presetType ) {
				args = await p21FlareoFlarePresetShape( presetType, args, [ globalDefaultColor ] );
			}

			// Run preset based on type and style.
			p21FlareoFlareRunPreset( args, presetStyle );

		} else { // Custom way.
			// Trigger confetti with args.
			await confetti( args);
		}
}

/**
 * Trigger flare on click of a selector.
 * 
 * @param {string} selector - The CSS selector for the target elements.
 * @param {Object} args - Configuration options for the flare effect.
 */
async function p21FlareoFlareOnClick( selector, args = {} ) {
	jQuery( selector ).on( 'click', async function(event) {
		try {
			if ( args && args.hasOwnProperty('flareTriggerTargetArea') && args.flareTriggerTargetArea ) {
				const rect = event.currentTarget.getBoundingClientRect()
				const x = rect.left + rect.width / 2
				const y = rect.top + rect.height / 2
				
				args.origin = {
					x: x / window.innerWidth,
					y: y / window.innerHeight,
				};
			}

			await p21FlareoFlareTrigger( args );
		} catch (error) {
			console.error("Flareo flare button error:", error)
		}
	} );
}

/**
 * Trigger flare on page load.
 * 
 * @param {Object} args - Configuration options for the flare effect.
 */
async function p21FlareoFlareOnPageLoad( args = {} ) {
	if ( args && args.hasOwnProperty('flareTriggerTargetArea') && args.hasOwnProperty('flareTriggerTargetSelector') && '' !== args.flareTriggerTargetSelector ) {
		const element = jQuery( args.flareTriggerTargetSelector );
		const rect = element[0].getBoundingClientRect()
		const x = rect.left + rect.width / 2
		const y = rect.top + rect.height / 2
		
		args.origin = {
			x: x / window.innerWidth,
			y: y / window.innerHeight,
		};
	}
	p21FlareoFlareTrigger( args );
}