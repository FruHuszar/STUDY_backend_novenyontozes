# Növény öntözési ütemező, értesítésekkel (push x email)

## Study jegyzet

1. Kártyák azonos magassággal, utolsó elem egy sorba

```css
#container {
  display: flex; /* A kártyák egymás mellé kerülnek */
  align-items: stretch; /* Alapértelmezett: minden kártya felveszi a legmagasabb magasságát */
}

.kartya {
  display: grid;
  grid-template-rows: 1fr auto 1fr; /* esetlegesen új sorba törő elem auto-ra*/
}

.kartya .utolso-elem {
  align-self: end; /* utolso elem a kártyán belül, legalulra rendeződik. */
}
```

2. Minél kisebb a képernyő, annál nagyobb a content

```css
#reszponziv-elem {
  width: clamp(65px, calc(160px - 7vw), 100px);
  /* Minimum: 65px (kijelző növekedésekor leáll)
     Ideális: Kivonás miatt fordított arányú méret
     Maximum: 100px (mobilon leáll, nem nő végtelenre) */
}
```

3. Saját esemény (CustomEvent) kibocsátása és adatátadás

```javascript
#attachEvents() {
this.#element.addEventListener('click', (event) => {
/* 1. (event): A böngésző automatikus kattintási eseménye (MouseEvent).
2. Nyílfüggvény () => {}: Nincs saját 'this'-e, így megőrzi a FlowerDisplay osztály kontextusát. */

const selectEvent = new CustomEvent('flowerSelect', {
  bubbles: true, /* Az esemény felgyűrűzik a DOM-ban a szülő elemekig (Esemény-delegálás) */
  detail: {
    card: this /* Kötelező 'detail' kulcs: ide csomagoljuk az átadandó adatot (itt a teljes kártya példányt) */
  }
});

this.#element.dispatchEvent(selectEvent); /* Elsüti a csomagot a kártya saját HTML elemén */

});
}
```

4. Egyedi esemény elcsípése a szülő konténeren

```javascript
#attachContainerEvents() {
this.#container.addEventListener('flowerSelect', (event) => {
/* A buborékolás miatt a szülő elcsípi az eseményt.
Az event.detail.card-ból kicsomagoljuk a kattintott kártya példányt. */
const selectedCard = event.detail.card;
this.#handleCardSelect(selectedCard);
});
}
```
