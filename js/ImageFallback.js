const PLACEHOLDER =
  "data:image/svg+xml;utf8," +
  encodeURIComponent(
    `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120">
       <rect width="120" height="120" fill="#e9ece2"/>
       <path d="M60 92c0-26 12-44 30-52-2 30-14 46-30 52z" fill="#8fa87a"/>
       <path d="M60 92c0-26-12-44-30-52 2 30 14 46 30 52z" fill="#7d9668"/>
       <rect x="58" y="88" width="4" height="18" rx="2" fill="#6b7f58"/>
     </svg>`,
  );

export default class ImageFallback {
  static src(url) {
    return url || PLACEHOLDER;
  }

  static attach(image) {
    if (!image) return;

    image.addEventListener(
      "error",
      () => {
        image.src = PLACEHOLDER;
      },
      { once: true },
    );
  }
}
