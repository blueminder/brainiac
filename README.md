# Brainiac

Engine for exploratory writing online written in PHP and using flat files for data storage.

## About

**Brainiac** acts almost as a "forced wiki", by cross-referencing written articles and automatically linking to them as the user navigates your site. In addition to this, the software also dynamically generates graphs in SVG detailing the relationships between the author's written articles and how often they are called by others.

Where microblogging has created a way for people to share their stream-of-consciousness in a linear fashion, the main idea behind **Brainiac** is allowing someone to view a 'snapshot' of somebody's thoughts at the time of site visitation. Not only can the visitor read about an author's thoughts about any topic, large or small, but they can also see where those thoughts fit in the bigger picture you wish to convey.

**Brainiac** was originally written for the 2008 Yahoo! HackU Competition at Georgia Tech by Enrique Santos & David Hollis. Since then, the code has had a much-needed cleaning, and now I'm working on polishing what's available and adding new features.

The original inspiration for **Brainiac** came from Sam Ward's sspeeps, and its ancestor pjpeeps by phat_joe (http://www.phatjoe.com/). Sadly, both installations can only be found on the Internet Archive at this point. Consider this an act of web application archaeology with improvements being added along the way.

## Known Issues

At the time of writing, the SVG visualizations only work in Mozilla Firefox. It seems as if Google Chrome does not have native SVG support available at this time.

## TODO

- Minor UI Tweaks (Mostly AJAXification of editing controls and updates)
- Interpeeps Support (http://thestormsurfer.livejournal.com/49436.html)

Copyright &copy; 2008-2011 Enrique Santos, David Hollis<br />
This program is released under the terms of the GNU General Public License v3.0
