#!/bin/sh
sudo apt-get install python-tk python-imaging x11-xserver-utils
sudo cat /etc/inittab | sed "s/2345:respawn/345:respawn/" >/etc/inittab
sudo echo "m:2:respawn:/usr/bin/manish.sh" >>/etc/inittab


pwd=`pwd`
sudo cat >/usr/bin/manish.sh << EOF
#!/bin/sh
export DISPLAY=:0
cd $pwd
xinit $pwd/gui.py -- $DISPLAY
chvt 1
/sbin/getty --noclear 38400 tty1
EOF

# http://raspberrypi.stackexchange.com/questions/752/how-do-i-prevent-the-screen-from-going-blank

sudo cat >/etc/X11/xinit/.xinitrc <<EOF
#!/bin/sh

# /etc/X11/xinit/.xinitrc
#
# global xinitrc file, used by all X sessions started by xinit (startx)

xset s off         # don't activate screensaver
xset -dpms         # disable DPMS (Energy Star) features.
xset s noblank     # don't blank the video device
# invoke global X session script
. /etc/X11/Xsession
EOF

echo Be sure to add a ping to backend.sh in the crontab
