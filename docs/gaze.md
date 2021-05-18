# Gaze
>Gaze, staring in the distance waiting for something to occur.

Gaze is a library that can be used for realtime server-to-client communication in a PHP project. It is split up into three modules: [GazeHub](gazehub.md), [GazeClient](gazeclient.md) and [GazePublisher](gazepublisher.md). See the [Complete install](complete-install.md) page for a detailed guide of how to install Gaze.

The modern web offers a lot of protocols to achieve realtime communication. Unfortunately, there are no plug-and-play solutions to integrate easily into an existing project. This is the problem that Gaze solves.

### How the Gaze modules fit together?

| Module | Usage |
| --- | --- |
| [GazeHub](gazehub.md) | Independent server, allows clients to listen for events and servers to send events to. |
| [GazeClient](gazeclient.md) | Frontend library, allows a browser to connect to GazeHub and subscribe to events. |
| [GazePublisher](gazepublisher.md) | Backend library, allows an application to send events to GazeHub. |
