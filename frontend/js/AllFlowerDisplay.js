import FlowerDisplay from "./FlowerDisplay.js";
import DetailPanel from "./DetailPanel.js";

export default class AllFlowerDisplay {
  #parent;
  #detailPanel;
  #cards = [];
  #activeCard = null;
  #tickHandle = null;

  constructor(parentSelector, detailPanelSelector, plantCards = []) {
    this.#parent = document.querySelector(parentSelector);

    this.#detailPanel = new DetailPanel(detailPanelSelector);

    this.#init(plantCards);
    this.#attachParentEvents();
    this.#startTicking();
  }

  #init(plantCards) {
    this.#parent.innerHTML = "";

    plantCards.forEach((plantData) => {
      const plant = new FlowerDisplay(plantData);

      this.#cards.push(plant);
      this.#parent.insertAdjacentElement("beforeend", plant.getElement());
    });
  }

  #attachParentEvents() {
    this.#parent.addEventListener("flowerSelect", (event) => {
      const selectedPlant = event.detail.card || event.detail;
      this.#handleCardSelect(selectedPlant);
    });
  }

  #startTicking() {
    this.#tickHandle = window.setInterval(() => {
      this.#cards.forEach((card) => card.refresh());
    }, 1000);
  }

  #handleCardSelect(selectedPlant) {
    if (this.#activeCard === selectedPlant) return;

    if (this.#activeCard) {
      this.#activeCard.setSelected(false);
    }
    this.#activeCard = selectedPlant;
    this.#activeCard.setSelected(true);

    this.#parent.classList.add("is-column");
    document.body.classList.add("has-detail");

    this.#detailPanel.display(selectedPlant.getData());
  }

  destroy() {
    window.clearInterval(this.#tickHandle);
    this.#tickHandle = null;
  }
}
