import Themes from '../common/Themes';
import { get } from './helpers/perDeviceSetting';

export default function getTheme() {
  const user = app.session.user;

  const IsUsingPerDeviceSettings = !user || !!user.preferences().fofNightMode_perDevice;
  const SelectedTheme = user && user.preferences().fofNightMode;

  let value;

  if (IsUsingPerDeviceSettings) {
    // fetch through LS is per device enabled
    value = get();
  } else {
    if (typeof SelectedTheme === 'number' && SelectedTheme !== -1) {
      // use user prefs
      value = SelectedTheme;
    }
  }

  return typeof value === 'number' ? value : Themes.DEFAULT();
}
