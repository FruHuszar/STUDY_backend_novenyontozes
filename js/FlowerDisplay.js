import TimeUtils from "./TimeUtils.js";

export default class FlowerDisplay {
  #data;
  #element;
  #timerNode;
  #alertNode;

  constructor(plantData) {
    this.#data = plantData;
    this.#element = this.#createElement();
    this.#attachEvents();
    this.refresh();
  }

  #createElement() {
    const card = document.createElement("div");
    card.classList.add("flower-card");
    card.dataset.id = this.#data.id;

    card.innerHTML = `
      <div class="flower-thumb">
        <img src="${this.#data.img}" alt="${this.#data.name}" />
        <span class="alert-dot" hidden></span>
      </div>
      <h3>${this.#data.name}</h3>
      <p class="flower-timer"></p>
    `;

    this.#timerNode = card.querySelector(".flower-timer");
    this.#alertNode = card.querySelector(".alert-dot");

    return card;
  }

  #attachEvents() {
    this.#element.addEventListener("click", () => {
      const selectEvent = new CustomEvent("flowerSelect", {
        bubbles: true,
        detail: {
          card: this,
        },
      });

      this.#element.dispatchEvent(selectEvent);
    });
  }

  refresh() {
    const due = TimeUtils.isDue(this.#data.nextWatering);
    const needsAttention = due || Boolean(this.#data.needsAttention);

    this.#timerNode.textContent = TimeUtils.format(this.#data.nextWatering);
    this.#timerNode.classList.toggle("is-due", due);
    this.#alertNode.hidden = !needsAttention;
    this.#alertNode.title = due ? "Needs water" : "Needs attention";
  }

  getElement() {
    return this.#element;
  }

  getData() {
    return this.#data;
  }

  setSelected(isSelected) {
    this.#element.classList.toggle("active", isSelected);
  }
}
