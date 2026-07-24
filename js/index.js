import AllFlowerDisplay from "./AllFlowerDisplay.js";

const HOUR = 3600000;
const DAY = 86400000;

const mockPlants = [
  {
    id: 1,
    name: "Taiwan rhododendron",
    latin: "Rhododendron formosanum Hemsl.",
    img: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRf23CbbN6hsDgI-RXUrg3sHG1ZTWFTPD1QJXaJNzax3w&s=10",
    nextWatering: new Date(Date.now() + 2 * DAY + 4 * HOUR),
    needsAttention: false,
    phases: {
      blooming: [4, 5],
      pruning: [10, 11],
      fruiting: [7, 8, 9],
    },
    facts: [
      { label: "Family", value: "Ericaceae" },
      { label: "Habitat", value: "Mid-altitude, island wide" },
      { label: "Light", value: "Partial shade" },
    ],
    note: "Rounded lobes with a slightly notched tip, red to brown spots in the throat.",
  },
  {
    id: 2,
    name: "Monstera",
    latin: "Monstera deliciosa Liebm.",
    img: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRf23CbbN6hsDgI-RXUrg3sHG1ZTWFTPD1QJXaJNzax3w&s=10",
    nextWatering: new Date(Date.now() - 30 * 60000),
    needsAttention: true,
    phases: {
      blooming: [6, 7],
      pruning: [3, 4],
      fruiting: [],
    },
    facts: [
      { label: "Family", value: "Araceae" },
      { label: "Habitat", value: "Indoor" },
      { label: "Light", value: "Bright indirect" },
    ],
    note: "Prefers an airy, free draining mix, wipe the leaves to keep them dust free.",
  },
  {
    id: 3,
    name: "Lavender",
    latin: "Lavandula angustifolia Mill.",
    img: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRf23CbbN6hsDgI-RXUrg3sHG1ZTWFTPD1QJXaJNzax3w&s=10",
    nextWatering: new Date(Date.now() + 11 * HOUR),
    needsAttention: false,
    phases: {
      blooming: [6, 7, 8],
      pruning: [3, 9],
      fruiting: [],
    },
    facts: [
      { label: "Family", value: "Lamiaceae" },
      { label: "Habitat", value: "Dry and sunny" },
      { label: "Light", value: "Full sun" },
    ],
    note: "Cut back after flowering to keep it compact, it does not tolerate overwatering.",
  },
];

export default new AllFlowerDisplay(
  "#flower-collection",
  "#flower-detail",
  mockPlants,
);
