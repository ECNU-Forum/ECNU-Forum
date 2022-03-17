import * as Cookies from 'es-cookie';
import Themes from '../../common/Themes';

export const KEY = 'flarum_nightmode';

export const get = () => {
  const value = Cookies.get(KEY);

  if (!value && value !== '0') {
    return Themes.DEFAULT();
  }

  return Number(value);
};

export const set = (val) =>
  Cookies.set(KEY, val, {
    sameSite: 'lax',
    secure: location.protocol === 'https:',
  });

export const remove = () => Cookies.remove(KEY);
