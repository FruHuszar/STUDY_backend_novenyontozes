import TimeUtils from "./TimeUtils.js";
import ImageFallback from "./ImageFallback.js";

export default class DetailPanel {
  static #MONTHS = [
    "JAN",
    "FEB",
    "MAR",
    "APR",
    "MAY",
    "JUN",
    "JUL",
    "AUG",
    "SEP",
    "OCT",
    "NOV",
    "DEC",
  ];

  static #PHASES = {
    blooming: { label: "Blooming", offset: 0 },
    pruning: { label: "Pruning", offset: 16 },
    fruiting: { label: "Fruiting", offset: 32 },
  };

  static #SVG_NS = "http://www.w3.org/2000/svg";
  static #SIZE = 360;
  static #CENTER = 180;
  static #RADIUS = 128;
  static #LABEL_RADIUS = 156;

  #element;
  #activePhases = { blooming: true, pruning: false, fruiting: false };
  #currentData = null;
  #portraitQuery;

  constructor(selector) {
    this.#element =
      typeof selector === "string"
        ? document.querySelector(selector)
        : selector;

    this.#portraitQuery = window.matchMedia("(orientation: portrait)");
    this.#portraitQuery.addEventListener("change", () => {
      if (this.#currentData) this.#renderTimeline();
    });
  }

  display(plantData) {
    if (!this.#element) return;
    this.#currentData = plantData;

    this.#renderSkeleton();
    ImageFallback.attach(this.#element.querySelector(".plant-center-img"));

    this.#renderTimeline();
    this.#attachToggleEvents();
    this.#attachWaterEvent();

    this.#element.classList.remove("is-entering");
    void this.#element.offsetWidth;
    this.#element.classList.add("is-visible", "is-entering");
  }

  #renderSkeleton() {
    const data = this.#currentData;
    const size = DetailPanel.#SIZE;

    this.#element.innerHTML = `
      <div class="detail-content">
        <div class="phase-toggles">
          ${Object.entries(DetailPanel.#PHASES)
            .map(
              ([key, phase]) => `
            <button type="button" class="toggle-btn ${key} ${this.#activePhases[key] ? "" : "off"}" data-phase="${key}" aria-pressed="${this.#activePhases[key]}" aria-label="${phase.label}">
              <span class="toggle-icon" aria-hidden="true"></span>
              <span class="toggle-tip">${phase.label}</span>
            </button>`,
            )
            .join("")}
        </div>
        <p class="phase-hint" aria-live="polite">${this.#hintText()}</p>

        <div class="timeline-wrapper">
          <img src="${ImageFallback.src(data.img)}" alt="${data.name}" class="plant-center-img" />
          <svg class="timeline-svg" viewBox="0 0 ${size} ${size}" role="img" aria-label="${data.name} yearly cycle">
            <circle cx="${DetailPanel.#CENTER}" cy="${DetailPanel.#CENTER}" r="${DetailPanel.#RADIUS}" class="timeline-base-circle" />
            <g class="months-group"></g>
            <g class="arcs-group"></g>
          </svg>
        </div>

        <h3>${data.name}</h3>
        <p class="latin">${data.latin || ""}</p>
        <p class="detail-timer ${TimeUtils.isDue(data.nextWatering) ? "is-due" : ""}">
          Next watering: <strong>${TimeUtils.format(data.nextWatering)}</strong>
        </p>
        <button type="button" class="water-btn">
          <span class="water-icon" aria-hidden="true"></span>
          <span class="water-label">Water now</span>
        </button>

        <dl class="detail-facts">
          ${(data.facts || [])
            .map((fact) => `<dt>${fact.label}</dt><dd>${fact.value}</dd>`)
            .join("")}
        </dl>
        <p class="detail-note">${data.note || ""}</p>
      </div>
    `;
  }

  #isHalf() {
    return this.#portraitQuery.matches;
  }

  #angleAt(monthIndex) {
    const sweep = this.#isHalf() ? -180 : 360;
    return -90 + (monthIndex / 12) * sweep;
  }

  #polarToCartesian(radius, angleInDegrees) {
    const rad = (angleInDegrees * Math.PI) / 180;
    return {
      x: DetailPanel.#CENTER + radius * Math.cos(rad),
      y: DetailPanel.#CENTER + radius * Math.sin(rad),
    };
  }

  #describeArc(radius, startAngle, endAngle) {
    const start = this.#polarToCartesian(radius, startAngle);
    const end = this.#polarToCartesian(radius, endAngle);
    const largeArc = Math.abs(endAngle - startAngle) > 180 ? 1 : 0;
    const sweep = endAngle > startAngle ? 1 : 0;

    return `M ${start.x} ${start.y} A ${radius} ${radius} 0 ${largeArc} ${sweep} ${end.x} ${end.y}`;
  }

  #toRanges(months) {
    const sorted = [...new Set(months)].sort((a, b) => a - b);

    return sorted.reduce((ranges, month) => {
      const last = ranges[ranges.length - 1];

      if (last && month === last[1] + 1) last[1] = month;
      else ranges.push([month, month]);

      return ranges;
    }, []);
  }

  #renderMonths(group) {
    DetailPanel.#MONTHS.forEach((month, index) => {
      const angle = this.#angleAt(index);
      const label = this.#polarToCartesian(DetailPanel.#LABEL_RADIUS, angle);
      const inner = this.#polarToCartesian(DetailPanel.#RADIUS - 7, angle);
      const outer = this.#polarToCartesian(DetailPanel.#RADIUS + 7, angle);
      const cos = Math.cos((angle * Math.PI) / 180);

      const tick = document.createElementNS(DetailPanel.#SVG_NS, "line");
      tick.setAttribute("x1", inner.x);
      tick.setAttribute("y1", inner.y);
      tick.setAttribute("x2", outer.x);
      tick.setAttribute("y2", outer.y);
      tick.setAttribute("class", "month-tick");
      group.appendChild(tick);

      const text = document.createElementNS(DetailPanel.#SVG_NS, "text");
      text.setAttribute("x", label.x);
      text.setAttribute("y", label.y);
      text.setAttribute("class", "month-text");
      text.setAttribute("dominant-baseline", "middle");
      text.setAttribute(
        "text-anchor",
        cos > 0.15 ? "start" : cos < -0.15 ? "end" : "middle",
      );
      text.textContent = month;
      group.appendChild(text);
    });
  }

  #renderArcs(group) {
    const phases = this.#currentData.phases || {};

    Object.entries(DetailPanel.#PHASES).forEach(([key, phase]) => {
      if (!this.#activePhases[key]) return;

      const radius = DetailPanel.#RADIUS - phase.offset;

      this.#toRanges(phases[key] || []).forEach(([from, to]) => {
        const path = document.createElementNS(DetailPanel.#SVG_NS, "path");
        path.setAttribute(
          "d",
          this.#describeArc(
            radius,
            this.#angleAt(from - 1.5),
            this.#angleAt(to - 0.5),
          ),
        );
        path.setAttribute("class", `phase-arc ${key}`);
        group.appendChild(path);
      });
    });
  }

  #renderTimeline() {
    const monthsGroup = this.#element.querySelector(".months-group");
    const arcsGroup = this.#element.querySelector(".arcs-group");
    if (!monthsGroup || !arcsGroup) return;

    monthsGroup.innerHTML = "";
    arcsGroup.innerHTML = "";

    this.#renderMonths(monthsGroup);
    this.#renderArcs(arcsGroup);
  }

  #hintText() {
    return Object.entries(DetailPanel.#PHASES)
      .map(
        ([key, phase]) =>
          `<span class="${this.#activePhases[key] ? "" : "off"}">${phase.label}</span>`,
      )
      .join(" · ");
  }

  #attachWaterEvent() {
    const button = this.#element.querySelector(".water-btn");

    if (!button) return;

    button.addEventListener("click", () => {
      const waterEvent = new CustomEvent("flowerWater", {
        bubbles: true,
        detail: { id: this.#currentData.id },
      });

      this.#element.dispatchEvent(waterEvent);
    });
  }

  setWatering(isWatering) {
    const button = this.#element.querySelector(".water-btn");

    if (!button) return;

    button.disabled = isWatering;
    button.querySelector(".water-label").textContent = isWatering
      ? "Watering..."
      : "Water now";
  }

  getElement() {
    return this.#element;
  }

  getData() {
    return this.#currentData;
  }

  #attachToggleEvents() {
    this.#element.querySelectorAll(".toggle-btn").forEach((button) => {
      button.addEventListener("click", (event) => {
        const target = event.currentTarget;
        const phase = target.dataset.phase;
        const active = !this.#activePhases[phase];

        this.#activePhases[phase] = active;
        target.classList.toggle("off", !active);
        target.setAttribute("aria-pressed", String(active));

        this.#element.querySelector(".phase-hint").innerHTML = this.#hintText();
        this.#renderTimeline();
      });
    });
  }
}
