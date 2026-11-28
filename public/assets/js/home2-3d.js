/* /home2: the laptop as a real 3D model (Three.js). Our own model, built here from simple shapes.
   home2.js still decides where the laptop is and what it is doing; it calls window.H2_3D.apply(state) with
     { x, y, w }   where the top-left corner and width of the screen (lid) should be, in CSS px
     lid           lid angle in degrees (90 = shut, 0 = open)
     base          0 = laptop, 1 = keyboard folded behind the screen (a monitor)
     tilt          a turn while it travels, degrees
   It renders only when something changed, lowers its own resolution if frames get slow, and hands back to the
   CSS laptop (event "h2-3d-lost") if the GPU drops the 3D context.
   The screen itself stays HTML (#dv .dv-screen): every frame it is mapped onto the 3D screen's four corners,
   so its text stays sharp and translatable. It turns towards the mouse when hovered. */
import * as THREE from 'three';
import { RoundedBoxGeometry } from 'three/addons/geometries/RoundedBoxGeometry.js';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

const screenEl = document.querySelector('#dv .dv-screen');
const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const SCREEN_W = 572, SCREEN_H = 346;            // the HTML screen's own size
const LID_W = 3.0, LID_H = 1.9, LID_T = 0.04;      // model units (1.9/3 = the slot's 380/600)
const BASE_W = 3.04, BASE_D = 1.9, BASE_T = 0.085;
const rad = (d) => d * Math.PI / 180;
const lerp = (a, b, t) => a + (b - a) * t;
const clamp = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);

function boot() {
  let renderer;
  try {
    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' });
  } catch (e) {
    return; // no WebGL: home2.js keeps the CSS laptop
  }
  if (!screenEl) return;
  let pixelRatio = Math.min(window.devicePixelRatio || 1, 1.5);
  renderer.setPixelRatio(pixelRatio);
  renderer.setSize(window.innerWidth, window.innerHeight);
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.05;
  const canvas = renderer.domElement;
  canvas.className = 'h2-canvas';
  canvas.setAttribute('aria-hidden', 'true');
  // The 3D layer (canvas + the laptop's HTML screen) lives in a host box: over the whole window while the laptop
  // travels between sections, or inside a section's slot while it rests there, so the page scrolls it natively.
  const host = document.createElement('div');
  host.className = 'h2-host';
  host.appendChild(canvas);
  document.body.appendChild(host);
  const dvEl = document.getElementById('dv');
  canvas.addEventListener('webglcontextlost', (e) => {
    e.preventDefault();
    stopped = true;
    host.remove();
    window.H2_3D = null;
    window.dispatchEvent(new Event('h2-3d-lost'));
  });
  let stopped = false;

  const scene = new THREE.Scene();
  const pmrem = new THREE.PMREMGenerator(renderer);
  scene.environment = pmrem.fromScene(new RoomEnvironment(renderer), 0.04).texture;
  const FOV = 28, CAM_Z = 24;
  const camera = new THREE.PerspectiveCamera(FOV, window.innerWidth / window.innerHeight, 0.1, 100);
  camera.position.set(0, 0, CAM_Z);
  const key = new THREE.DirectionalLight(0xffffff, 1.4);
  key.position.set(-4, 7, 9);
  scene.add(key, new THREE.AmbientLight(0xdfe8f0, 0.35));

  /* ---------- Materials ---------- */
  const alu = new THREE.MeshStandardMaterial({ color: 0xd3d8dd, metalness: 0.92, roughness: 0.3 });
  const aluDark = new THREE.MeshStandardMaterial({ color: 0xaab3bb, metalness: 0.9, roughness: 0.35 });
  const keyMat = new THREE.MeshStandardMaterial({ color: 0x2b3036, metalness: 0.25, roughness: 0.55 });
  const padMat = new THREE.MeshStandardMaterial({ color: 0xc3cad0, metalness: 0.7, roughness: 0.18 });
  const bezel = new THREE.MeshStandardMaterial({ color: 0x0c1014, metalness: 0.2, roughness: 0.35 });
  const glass = new THREE.MeshStandardMaterial({ color: 0x0a0f14, metalness: 0.1, roughness: 0.15 });

  /* ---------- The model: root (position/scale/turn) > lid (hinged) + base (hinged) ---------- */
  const root = new THREE.Group();
  scene.add(root);

  // Lid: hinge at the origin, the screen faces +z when open
  const lid = new THREE.Group();
  root.add(lid);
  // A modern lid: a thin aluminium shell, a black glass front with rounded corners, and a screen that fills almost
  // all of it (slim even bezels), with the camera in the top bezel.
  const lidBody = new THREE.Mesh(new RoundedBoxGeometry(LID_W, LID_H, LID_T, 4, 0.05), alu);
  lidBody.position.set(0, LID_H / 2 + 0.012, -LID_T / 2 - 0.004);
  lid.add(lidBody);
  const bezelMesh = new THREE.Mesh(new RoundedBoxGeometry(LID_W - 0.016, LID_H - 0.016, 0.006, 3, 0.045), bezel);
  bezelMesh.position.set(0, LID_H / 2 + 0.012, -0.001);   // its front face sits at z = 0.002
  lid.add(bezelMesh);
  // The screen area: same proportions as the HTML screen; its corners are where that screen is drawn
  const SW = 2.92, SH = SW * SCREEN_H / SCREEN_W;           // 0.04 bezel left/right
  const screenMesh = new THREE.Mesh(new THREE.PlaneGeometry(SW, SH), glass);
  const BOTTOM_BEZEL = 0.046;
  const screenCenterY = 0.012 + 0.008 + BOTTOM_BEZEL + SH / 2; // thin chin; the top bezel (a little taller) holds the camera
  screenMesh.position.set(0, screenCenterY, 0.0028);
  lid.add(screenMesh);
  const camY = (screenCenterY + SH / 2 + LID_H + 0.012) / 2;  // centred in the top bezel
  const camRing = new THREE.Mesh(new THREE.CircleGeometry(0.02, 24), new THREE.MeshStandardMaterial({ color: 0x1a2128, roughness: 0.3 }));
  camRing.position.set(0, camY, 0.0026);
  lid.add(camRing);
  const camLens = new THREE.Mesh(new THREE.CircleGeometry(0.011, 20), new THREE.MeshStandardMaterial({ color: 0x0e2a44, metalness: 0.6, roughness: 0.1, emissive: 0x0a1c30 }));
  camLens.position.set(0, camY, 0.0029);
  lid.add(camLens);
  const camGlint = new THREE.Mesh(new THREE.CircleGeometry(0.0035, 10), new THREE.MeshBasicMaterial({ color: 0x9fd4ff }));
  camGlint.position.set(-0.004, camY + 0.004, 0.003);
  lid.add(camGlint);
  const hinge = new THREE.Mesh(new THREE.CylinderGeometry(0.045, 0.045, LID_W * 0.86, 20), aluDark);
  hinge.rotation.z = Math.PI / 2;
  hinge.position.set(0, -0.005, -0.03);
  root.add(hinge);

  // Base: hinge at the origin, extends towards the viewer (+z)
  const base = new THREE.Group();
  root.add(base);
  const baseBody = new THREE.Mesh(new RoundedBoxGeometry(BASE_W, BASE_T, BASE_D, 4, 0.035), alu);
  baseBody.position.set(0, -BASE_T / 2, BASE_D / 2);
  base.add(baseBody);
  // keyboard: 5 rows x 13 keys plus a space bar
  const keyGeo = new THREE.BoxGeometry(0.165, 0.016, 0.15);
  const keys = new THREE.InstancedMesh(keyGeo, keyMat, 5 * 13 + 1);
  const m = new THREE.Matrix4();
  let k = 0;
  for (let row = 0; row < 5; row++) {
    for (let col = 0; col < 13; col++) {
      m.makeTranslation(-1.14 + col * 0.19, 0.004, 0.2 + row * 0.18);
      keys.setMatrixAt(k++, m);
    }
  }
  m.compose(new THREE.Vector3(0, 0.004, 0.2 + 5 * 0.18), new THREE.Quaternion(), new THREE.Vector3(5.2, 1, 1));
  keys.setMatrixAt(k, m);
  base.add(keys);
  const pad = new THREE.Mesh(new THREE.BoxGeometry(1.05, 0.006, 0.52), padMat);
  pad.position.set(0, 0.001, 1.45);
  base.add(pad);
  const lip = new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.02, 0.03), aluDark);
  lip.position.set(0, -BASE_T + 0.03, BASE_D + 0.005);
  base.add(lip);

  // Soft shadow under the laptop
  const sc = document.createElement('canvas');
  sc.width = sc.height = 128;
  const g = sc.getContext('2d');
  const grad = g.createRadialGradient(64, 64, 4, 64, 64, 64);
  grad.addColorStop(0, 'rgba(15,34,54,.42)');
  grad.addColorStop(1, 'rgba(15,34,54,0)');
  g.fillStyle = grad;
  g.fillRect(0, 0, 128, 128);
  const shadow = new THREE.Mesh(new THREE.PlaneGeometry(4.6, 2.6), new THREE.MeshBasicMaterial({ map: new THREE.CanvasTexture(sc), transparent: true, depthWrite: false }));
  shadow.rotation.x = -Math.PI / 2;
  shadow.position.set(0, -BASE_T - 0.02, BASE_D / 2);
  root.add(shadow);

  /* ---------- State from home2.js ---------- */
  let state = null;
  const hover = { x: 0, y: 0, tx: 0, ty: 0 }; // the turn towards the mouse (eased)
  let bbox = null; // the screen's area inside the host, for hover

  /* ---------- Where the layer lives ---------- */
  let anchorEl = null, anchorW = -1, hostW = 0, hostH = 0, slotLocal = null;
  function sizeTo(w, h) {
    w = Math.max(1, Math.round(w)); h = Math.max(1, Math.round(h));
    if (w === hostW && h === hostH) return;
    hostW = w; hostH = h;
    renderer.setSize(w, h);
  }
  function rehost(anchor) {
    if (anchor) {
      // inside the slot, with room around it for the keyboard, the shadow and the hover turn
      const w = anchor.clientWidth, h = w * 380 / 600;
      const mx = Math.round(w * .5), mt = Math.round(w * .35), mb = Math.round(w * .62);
      host.className = 'h2-host is-anchored';
      host.style.left = -mx + 'px';
      host.style.top = -mt + 'px';
      host.style.width = (w + 2 * mx) + 'px';
      host.style.height = Math.round(h + mt + mb) + 'px';
      if (host.parentNode !== anchor) anchor.appendChild(host);
      slotLocal = { x: mx, y: mt, w };
      sizeTo(w + 2 * mx, h + mt + mb);
    } else {
      host.className = 'h2-host';
      host.style.left = host.style.top = '0px';
      host.style.width = window.innerWidth + 'px';
      host.style.height = window.innerHeight + 'px';
      if (host.parentNode !== document.body) document.body.appendChild(host);
      slotLocal = null;
      sizeTo(window.innerWidth, window.innerHeight);
    }
    if (dvEl && dvEl.parentNode !== host) host.appendChild(dvEl);
    anchorEl = anchor;
    anchorW = anchor ? anchor.clientWidth : -1;
  }

  // Draws right away, in the same frame as the scroll that asked for it (so the laptop and the page never drift).
  function apply(s) {
    const a = s.anchor || null;
    // resting in a slot with nothing changed: the page scrolls it, nothing to redraw
    if (state && a && a === anchorEl && a.clientWidth === anchorW && s.lid === state.lid && s.base === state.base && s.tilt === state.tilt) { state = s; return; }
    state = s;
    if (a !== anchorEl || (a && a.clientWidth !== anchorW) || (!a && (hostW !== window.innerWidth || hostH !== window.innerHeight))) rehost(a);
    draw();
  }

  function layout() {
    const s = state;
    const W = hostW, H = hostH;
    const ppu = H / (2 * CAM_Z * Math.tan(rad(FOV) / 2));
    const r = slotLocal || { x: s.x, y: s.y, w: s.w };        // the slot inside the host
    // the slot is the whole lid (frame included): its width sets the size, its top is the lid's top edge
    const scale = r.w / (LID_W * ppu);
    const hingeX = r.x + r.w / 2;
    const hingeY = r.y + (LID_H + 0.012) * scale * ppu;
    root.position.set((hingeX - W / 2) / ppu, -(hingeY - H / 2) / ppu, 0);
    root.scale.setScalar(scale);

    // The camera looks straight at the laptop (at the middle of its screen) wherever it is on the page, so it is
    // always seen dead centre, never from the side. A lens shift (setViewOffset) keeps it drawn at its spot.
    const ax = hingeX, ay = hingeY - (LID_H / 2) * scale * ppu;
    camera.position.set((ax - W / 2) / ppu, -(ay - H / 2) / ppu, CAM_Z);
    camera.aspect = W / H;
    camera.setViewOffset(W, H, W / 2 - ax, H / 2 - ay, W, H); // (also updates the projection)

    const fold = clamp(s.base);
    // a touch from above as a laptop (the keyboard just shows), straight on as a monitor; plus the travel turn
    root.rotation.set(lerp(0.07, 0.0, fold) + hover.y, rad(s.tilt) + hover.x, 0);
    lid.rotation.x = rad(s.lid) - 0.1 * (1 - s.lid / 90);
    // the keyboard swings down, back and up behind the screen (like a 360-degree convertible)
    base.rotation.x = rad(270 * fold);
    base.position.z = -0.15 * clamp((fold - 0.6) / 0.4);
    shadow.material.opacity = (1 - fold) * 0.9;
  }

  /* The HTML screen follows the 3D screen: project its four corners and map the element onto them. */
  const corners = [new THREE.Vector3(), new THREE.Vector3(), new THREE.Vector3(), new THREE.Vector3()];
  let lastPts = null;
  function placeScreen() {
    const hw = SW / 2, hh = SH / 2;
    const local = [[-hw, hh], [hw, hh], [hw, -hh], [-hw, -hh]]; // TL TR BR BL
    const W = hostW, H = hostH;
    const pts = local.map(([x, y], i) => {
      const v = corners[i].set(x, y, 0.0031); // on the glass
      screenMesh.localToWorld(v);
      v.project(camera);
      return [(v.x + 1) / 2 * W, (1 - v.y) / 2 * H];
    });
    // facing away (lid shut or turned) -> hide the HTML screen
    const area = (pts[1][0] - pts[0][0]) * (pts[3][1] - pts[0][1]) - (pts[3][0] - pts[0][0]) * (pts[1][1] - pts[0][1]);
    const facing = area > 40 && state.lid < 78; // (the HTML screen hides while the lid is nearly shut)
    screenEl.style.visibility = facing ? 'visible' : 'hidden';
    lastPts = pts;
    const xs = pts.map((p) => p[0]), ys = pts.map((p) => p[1]);
    bbox = { l: Math.min(...xs), r: Math.max(...xs), t: Math.min(...ys), b: Math.max(...ys) };
    if (!facing) return;
    screenEl.style.transform = homography([[0, 0], [SCREEN_W, 0], [SCREEN_W, SCREEN_H], [0, SCREEN_H]], pts);
  }

  /* ---------- Hover: the laptop turns towards the mouse while it is over it, and settles back after ---------- */
  if (!reduce) {
    window.addEventListener('pointermove', (e) => {
      if (!bbox || e.pointerType === 'touch') return;
      const hr = host.getBoundingClientRect(), px = e.clientX - hr.left, py = e.clientY - hr.top; // into the host
      const padX = (bbox.r - bbox.l) * 0.35, padY = (bbox.b - bbox.t) * 0.6;
      const inside = px > bbox.l - padX && px < bbox.r + padX && py > bbox.t - padY && py < bbox.b + padY;
      if (inside) {
        hover.tx = ((px - (bbox.l + bbox.r) / 2) / (bbox.r - bbox.l)) * 0.7;
        hover.ty = ((py - (bbox.t + bbox.b) / 2) / (bbox.b - bbox.t)) * 0.22;
      } else {
        hover.tx = 0; hover.ty = 0;
      }
    }, { passive: true });
    document.addEventListener('pointerleave', () => { hover.tx = 0; hover.ty = 0; });
  }

  window.addEventListener('resize', () => {
    if (!state) return;
    rehost(anchorEl);
    draw();
  });

  let slow = 0;
  function draw() {
    if (!state || stopped) return;
    layout();
    const t0 = performance.now();
    renderer.render(scene, camera);
    placeScreen();
    // Slow frames several times in a row: draw at a lower resolution (down to 1x) to stay smooth
    slow = performance.now() - t0 > 10 ? slow + 1 : Math.max(0, slow - 1);
    if (slow > 8 && pixelRatio > 1) {
      pixelRatio = Math.max(1, pixelRatio - 0.25);
      renderer.setPixelRatio(pixelRatio);
      const w = hostW, h = hostH;
      hostW = hostH = 0;
      sizeTo(w, h);
      slow = 0;
    }
  }

  // corners(): the screen's four corners on the page (for checks)
  const pageCorners = () => {
    if (!lastPts) return null;
    const hr = host.getBoundingClientRect();
    return lastPts.map(([x, y]) => [x + hr.left, y + hr.top]);
  };
  // The hover turn eases on its own (same speed at any frame rate) and redraws only while it is moving
  let last = 0;
  function frame(now) {
    if (stopped) return;
    const dt = last ? Math.min(64, now - last) : 16.7;
    last = now;
    const dx = hover.tx - hover.x, dy = hover.ty - hover.y;
    if (Math.abs(dx) > 0.0005 || Math.abs(dy) > 0.0005) {
      const k = 1 - Math.pow(0.92, dt / 16.7);
      hover.x += dx * k;
      hover.y += dy * k;
      draw();
    }
    window.requestAnimationFrame(frame);
  }
  window.requestAnimationFrame(frame);

  window.H2_3D = { apply, corners: pageCorners };
  window.dispatchEvent(new Event('h2-3d-ready'));
}

/* Maps a rectangle (src corners) onto any four points (dst) with a CSS matrix3d, the same maths the home page hero
   uses for its screens. Corner order: TL, TR, BR, BL. */
function homography(src, dst) {
  const adj = (m) => [m[4] * m[8] - m[5] * m[7], m[2] * m[7] - m[1] * m[8], m[1] * m[5] - m[2] * m[4],
    m[5] * m[6] - m[3] * m[8], m[0] * m[8] - m[2] * m[6], m[2] * m[3] - m[0] * m[5],
    m[3] * m[7] - m[4] * m[6], m[1] * m[6] - m[0] * m[7], m[0] * m[4] - m[1] * m[3]];
  const mul = (a, b) => {
    const c = [];
    for (let i = 0; i < 3; i++) for (let j = 0; j < 3; j++) c[3 * i + j] = a[3 * i] * b[j] + a[3 * i + 1] * b[3 + j] + a[3 * i + 2] * b[6 + j];
    return c;
  };
  const mulv = (m, v) => [m[0] * v[0] + m[1] * v[1] + m[2] * v[2], m[3] * v[0] + m[4] * v[1] + m[5] * v[2], m[6] * v[0] + m[7] * v[1] + m[8] * v[2]];
  const basis = (p) => {
    const mm = [p[0][0], p[1][0], p[2][0], p[0][1], p[1][1], p[2][1], 1, 1, 1];
    const v = mulv(adj(mm), [p[3][0], p[3][1], 1]);
    return mul(mm, [v[0], 0, 0, 0, v[1], 0, 0, 0, v[2]]);
  };
  // corner order for the maths: TL, TR, BL, BR
  const s = basis([src[0], src[1], src[3], src[2]]);
  const d = basis([dst[0], dst[1], dst[3], dst[2]]);
  const t = mul(d, adj(s));
  for (let i = 0; i < 9; i++) t[i] = t[i] / t[8];
  return 'matrix3d(' + [t[0], t[3], 0, t[6], t[1], t[4], 0, t[7], 0, 0, 1, 0, t[2], t[5], 0, t[8]].join(',') + ')';
}

boot();
