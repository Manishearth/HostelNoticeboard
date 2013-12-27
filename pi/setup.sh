#!/bin/sh
sudo apt-get install python-tk python-imaging
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

