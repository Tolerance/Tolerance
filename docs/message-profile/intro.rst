Introduction
============

An application consume and produce messages from and to other services. These messages can be HTTP messages, AMQP
messages, SQL queries and so on. When the application grows it can become a pain to understand and follow the different
messages of a given transaction.

The goal of this component is to be able to track all the messages to be able to debug and monitor the application
interaction. Once you've setup the component you can use the `Viewer application <viewer.html>`_ to inspect some transactions.

.. image:: ../_static/tolerance-viewer-preview-1.gif

