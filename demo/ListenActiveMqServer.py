#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys
import time
import datetime
import subprocess
import signal
import smtplib
from email.mime.text import MIMEText


PID = 0

def send_mail(content):

    global version
    _user = "455640637@qq.com"
    _pwd = "yspoarrcsetncadg"
    _to = "455640637@qq.com"

    path = os.getcwd().split("/")[-1]

    if path == 'BFLMobileMall':
        version = 'DEV'
    else:
        version = 'MASTER'

    msg = MIMEText(content + str(datetime.datetime.now()))
    msg["Subject"] = "Babyfunlab -" + version
    msg["From"] = _user
    msg["To"] = _to

    try:
        s = smtplib.SMTP_SSL("smtp.qq.com", 465)
        s.login(_user, _pwd)
        s.sendmail(_user, _to, msg.as_string())
        s.quit()
        # print "Success!"
    except smtplib.SMTPException, e:
        pass
        # print "Falied,%s" % e


def write_pid():
    with open('listen_pid.ini', 'w+') as f:
        f.write(str(PID))


def read_pid():
    if not os.path.exists('listen_pid.ini'):
        return 0

    global PID
    line = open('listen_pid.ini').readline()
    if line == '':
        PID = 0
    else:
        PID = int(line)


def cb(s, f):
    global PID
    os.kill(-PID, 9)
    PID = 0
    write_pid()
    send_mail('activeMQ Listen Server stop ')
    print 'recv signal', s
    # close at here
    exit(0)


def files_to_timestamp(files):
    return dict([(f, os.path.getmtime(f)) for f in files])


def add():
    pass


def remov():
    pass


def modifi():
    mq_listen_start()

def check_pid(pid):        
    """ Check For the existence of a unix pid. """
    try:
        os.kill(pid, 0)
    except OSError:
        return False
    else:
        return True

def mq_listen_start():
    global PID
    if PID > 0 and check_pid(PID):
        send_mail('queue config is update , daemon.php is restart running ')

    p = subprocess.Popen("php daemon.php", shell=True, preexec_fn=os.setpgrp)
    PID = p.pid
    write_pid()

def check_activeMq_running():
    if version == 'DEV':
        processPid = 'ActiveMQ.pid'
    elif version == 'MASTER':
        processPid = 'ActiveMQ-Master.pid'

    result = subprocess.check_output('ps -ef|grep '+processPid, shell=True).decode('utf-8').split('\n')
    if len(result) > 3:
        return True
    else:
        send_mail('activeMQ Server Not Running ')
        exit(0)


def listen():

    read_pid()
    mq_listen_start()

    path_to_watch = ['console/config/queue.php']
    # print "Watching ", path_to_watch

    before = files_to_timestamp(path_to_watch)

    send_mail('activeMQ Listen Server start ')

    while 1:
        check_activeMq_running()

        time.sleep(2)
        after = files_to_timestamp(path_to_watch)

        added = [f for f in after.keys() if not f in before.keys()]
        removed = [f for f in before.keys() if not f in after.keys()]
        modified = []

        for f in before.keys():
            if not f in removed:
                if os.path.getmtime(f) != before.get(f):
                    modified.append(f)

        if added:
            add()
        if removed:
            remov()
        if modified:
            modifi()

        before = after


if __name__ == "__main__":

    signal.signal(signal.SIGTERM, cb)
    signal.signal(signal.SIGINT, cb)
    listen()
