import FlowerDisplay from "./FlowerDisplay.js";
import DetailPanel from "./DetailPanel.js";

export default class AllFlowerDisplay {
  #parent;
  #detailPanel;
  #client;
  #cards = [];
  #activeCard = null;
  #tickHandle = null;

  constructor(parentSelector, detailPanelSelector, plantCards = [], client = null) {
    this.#parent = document.querySelector(parentSelector);
    this.#detailPanel = new DetailPanel(detailPanelSelector);
    this.#client = client;

    this.#init(plantCards);
    this.#attachParentEvents();
    this.#attachDetailEvents();
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

  #attachDetailEvents() {
    this.#detailPanel.getElement().addEventListener("flowerWater", (event) => {
      this.#handleWater(event.detail.id);
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

  async #handleWater(plantId) {
    if (!this.#client) return;

    const card = this.#cards.find((candidate) => candidate.getData().id === plantId);

    if (!card) return;

    this.#detailPanel.setWatering(true);

    try {
      const updated = await this.#client.water(plantId);

      card.update(updated);
      this.#detailPanel.display(card.getData());
      this.#detailPanel.setWatering(false);
    } catch (error) {
      this.#detailPanel.setWatering(false);
      window.alert(`Watering failed: ${error.message}`);
    }
  }

  destroy() {
    window.clearInterval(this.#tickHandle);
    this.#tickHandle = null;
  }
}
