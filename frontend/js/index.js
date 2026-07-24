import AllFlowerDisplay from "./AllFlowerDisplay.js";
import ApiClient from "./ApiClient.js";
import MockApiClient from "./MockApiClient.js";
import mockPlants from "./mockPlants.js";

const API_BASE_URL = "http://localhost:8000";
const USER_ID = 1;

function toggleTrialNote(isBackendUp) {
  const note = document.querySelector("#trial-note");

  if (note) note.hidden = isBackendUp;
}

async function resolveSource() {
  const api = new ApiClient(API_BASE_URL);

  try {
    const plants = await api.getPlants(USER_ID);

    return { client: api, plants, isBackendUp: true };
  } catch (error) {
    const mock = new MockApiClient(mockPlants);

    return { client: mock, plants: await mock.getPlants(), isBackendUp: false };
  }
}

async function start() {
  const source = await resolveSource();

  toggleTrialNote(source.isBackendUp);

  return new AllFlowerDisplay(
    "#flower-collection",
    "#flower-detail",
    source.plants,
    source.client,
  );
}

start();
