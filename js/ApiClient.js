export default class ApiClient {
  #baseUrl;

  constructor(baseUrl) {
    this.#baseUrl = baseUrl.replace(/\/$/, "");
  }

  async getPlants(userId) {
    return this.#request(`/api/plants?userId=${userId}`);
  }

  async water(plantId, amountMl = null) {
    await this.#request(`/api/plants/${plantId}/waterings`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ amountMl, source: "manual" }),
    });

    return this.#request(`/api/plants/${plantId}`);
  }

  async #request(path, options = {}) {
    const response = await fetch(`${this.#baseUrl}${path}`, options);

    if (!response.ok) {
      throw new Error(`${response.status} ${response.statusText}`);
    }

    return response.json();
  }
}
