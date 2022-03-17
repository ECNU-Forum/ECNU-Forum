/*
    This function is designed to catch invalid theme values
    and handle them before they can break Flarum for users.

    E.g. if a user manually edited their cookies to
    set their theme to an invalid value, this theme would
    detect that, and reset it to 0 (auto).
*/

import Themes from '../common/Themes';
import { get, set } from './helpers/perDeviceSetting';

export default function fixInvalidThemeSetting() {
  // get array of valid values without duplicate entries
  let validValues = Array.from(new Set(Object.values(Themes)));

  const Theme = get();

  if (isNaN(Theme)) {
    resetTheme('Theme is not a valid integer! Resetting...');
  } else if (!validValues.includes(Theme)) {
    // theme out of bounds
    resetTheme(`Theme is out of bounds! Resetting...`);
  }
}

function resetTheme(reason) {
  console.warn(reason);

  set(Themes.DEFAULT());
}
