const HOUR = 3600000;
const DAY = 86400000;

export default [
  {
    id: 1,
    name: "Taiwan Rhododendron",
    latin: "Rhododendron formosanum Hemsl.",
    img: "https://images.unsplash.com/photo-1596724803525-4c07c2a74c43?auto=format&fit=crop&w=800&q=80",
    nextWatering: new Date(Date.now() + 2 * DAY + 4 * HOUR),
    needsAttention: false,
    wateringIntervalHours: 72,
    phases: {
      blooming: [4, 5],
      pruning: [6, 7], // Prune immediately after spring blooming
      fruiting: [],
    },
    facts: [
      { label: "Family", value: "Ericaceae" },
      { label: "Habitat", value: "Subtropical Mountain" },
      { label: "Light", value: "Partial shade" },
    ],
    note: "Prefers acidic, well-draining soil. Avoid heavy pruning late in the year to protect developing winter buds.",
  },
  {
    id: 2,
    name: "Swiss Cheese Plant",
    latin: "Monstera deliciosa Liebm.",
    img: "https://images.unsplash.com/photo-1614594975525-e45190c55d0b?auto=format&fit=crop&w=800&q=80",
    nextWatering: new Date(Date.now() - 30 * 60000), // Overdue by 30 mins to show attention flag
    needsAttention: true,
    wateringIntervalHours: 168,
    phases: {
      blooming: [6, 7],
      pruning: [3, 4, 5], // Prune during spring growth boost
      fruiting: [],
    },
    facts: [
      { label: "Family", value: "Araceae" },
      { label: "Habitat", value: "Indoor Tropical" },
      { label: "Light", value: "Bright indirect" },
    ],
    note: "Allow top 2 inches of soil to dry out between waterings. Wipe leaves regularly with a damp cloth to clear dust.",
  },
  {
    id: 3,
    name: "English Lavender",
    latin: "Lavandula angustifolia Mill.",
    img: "https://images.unsplash.com/photo-1528183429752-a97d0bf99b5a?auto=format&fit=crop&w=800&q=80",
    nextWatering: new Date(Date.now() + 11 * HOUR),
    needsAttention: false,
    wateringIntervalHours: 240,
    phases: {
      blooming: [6, 7, 8],
      pruning: [4, 9], // Light spring prune, main prune after flowering in autumn
      fruiting: [],
    },
    facts: [
      { label: "Family", value: "Lamiaceae" },
      { label: "Habitat", value: "Mediterranean Garden" },
      { label: "Light", value: "Full sun" },
    ],
    note: "Highly drought tolerant once established. Requires gritty, fast-draining soil to prevent root rot.",
  },
  {
    id: 4,
    name: "Orchid (Phalaenopsis)",
    latin: "Phalaenopsis blume",
    img: "https://images.unsplash.com/photo-1525310072745-f49212b5ac6d?auto=format&fit=crop&w=800&q=80",
    nextWatering: new Date(Date.now() + 5 * DAY),
    needsAttention: false,
    wateringIntervalHours: 168,
    phases: {
      blooming: [1, 2, 3, 11, 12], // Late autumn to early spring
      pruning: [4, 5], // Trim spent flower spikes after bloom season ends
      fruiting: [],
    },
    facts: [
      { label: "Family", value: "Orchidaceae" },
      { label: "Habitat", value: "Epiphytic Indoor" },
      { label: "Light", value: "Medium indirect" },
    ],
    note: "Water using bark-soaked immersion or coarse bark-mix drainage. Roots turn greyish-white when thirsty.",
  },
  {
    id: 5,
    name: "Peace Lily",
    latin: "Spathiphyllum wallisii",
    img: "https://images.unsplash.com/photo-1593691509543-c55fb32e7355?auto=format&fit=crop&w=800&q=80",
    nextWatering: new Date(Date.now() - 2 * HOUR), // Overdue to show attention state
    needsAttention: true,
    wateringIntervalHours: 120,
    phases: {
      blooming: [3, 4, 5, 6],
      pruning: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], // Trim yellowed leaves and spent spadices as needed year-round
      fruiting: [],
    },
    facts: [
      { label: "Family", value: "Araceae" },
      { label: "Habitat", value: "Indoor Tropical" },
      { label: "Light", value: "Low to bright indirect" },
    ],
    note: "Visibly wilts when dry but recovers fast after watering. Sensitive to fluoride in tap water.",
  },
  {
    id: 6,
    name: "Hibiscus",
    latin: "Hibiscus rosa-sinensis",
    img: "https://images.unsplash.com/photo-1550950158-d0d960dff51b?auto=format&fit=crop&w=800&q=80",
    nextWatering: new Date(Date.now() + 1 * DAY + 12 * HOUR),
    needsAttention: false,
    wateringIntervalHours: 48,
    phases: {
      blooming: [6, 7, 8, 9],
      pruning: [3], // Major hard prune in early spring to encourage new flowering stems
      fruiting: [],
    },
    facts: [
      { label: "Family", value: "Malvaceae" },
      { label: "Habitat", value: "Tropical Garden / Patio" },
      { label: "Light", value: "Full sun" },
    ],
    note: "Heavy feeder and drinker during summer blooming. Prune hard in spring to stimulate massive flower yields.",
  },
  {
    id: 7,
    name: "Bird of Paradise",
    latin: "Strelitzia reginae",
    img: "https://images.unsplash.com/photo-1508610048659-a06b669e3321?auto=format&fit=crop&w=800&q=80",
    nextWatering: new Date(Date.now() + 4 * DAY),
    needsAttention: false,
    wateringIntervalHours: 192,
    phases: {
      blooming: [9, 10, 11, 12, 1, 2], // Winter bloomer
      pruning: [4, 5], // Remove old bottom leaves in spring
      fruiting: [],
    },
    facts: [
      { label: "Family", value: "Strelitziaceae" },
      { label: "Habitat", value: "Subtropical / Indoor" },
      { label: "Light", value: "Direct bright sun" },
    ],
    note: "Requires lots of light to bloom successfully indoors. Keep soil consistently moist in summer, drier in winter.",
  },
  {
    id: 8,
    name: "African Violet",
    latin: "Saintpaulia ionantha",
    img: "https://images.unsplash.com/photo-1628151016027-53772b21c606?auto=format&fit=crop&w=800&q=80",
    nextWatering: new Date(Date.now() + 3 * DAY),
    needsAttention: false,
    wateringIntervalHours: 96,
    phases: {
      blooming: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], // Can bloom almost year-round indoors
      pruning: [3, 9], // Pinch outer aging leaves twice a year
      fruiting: [],
    },
    facts: [
      { label: "Family", value: "Gesneriaceae" },
      { label: "Habitat", value: "Indoor Desktop" },
      { label: "Light", value: "Filtered bright indirect" },
    ],
    note: "Bottom water to prevent leaf spots. Prefers room-temperature water and small pots to encourage blooming.",
  },
];
