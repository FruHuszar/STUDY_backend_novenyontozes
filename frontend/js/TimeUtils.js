export default class TimeUtils {
  static remaining(target) {
    const diff = new Date(target).getTime() - Date.now();
    return diff > 0 ? diff : 0;
  }

  static isDue(target) {
    return TimeUtils.remaining(target) === 0;
  }

  static format(target) {
    const diff = TimeUtils.remaining(target);

    if (diff === 0) return "Needs water";

    const days = Math.floor(diff / 86400000);
    const hours = Math.floor(diff / 3600000) % 24;
    const minutes = Math.floor(diff / 60000) % 60;
    const seconds = Math.floor(diff / 1000) % 60;

    if (days > 0) return `${days}d ${hours}h ${minutes}m`;

    return `${hours}h ${minutes}m ${seconds}s`;
  }
}
