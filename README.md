# Doctrine Tree Extension

This library contains an extension for Doctrine that makes it quick and easy to add hierarchical tree structures to your entities. With just a couple of configurations you can start managing your tree structures effortlessly.

**Version 1.0.0**

[![Build Status](https://travis-ci.org/theartoflogic/doctrine-tree-extension.png)](https://travis-ci.org/theartoflogic/doctrine-tree-extension)

### Features

**Note:** At this time Closure trees are the only supported method. I am actively developing support for other tree models and will release an updates as soon as they are ready.

- **Closure Tree**: Easily manage the closure table, including the option to specify the depth of each node.

### Running the tests:

To run the tests follow these instructions:

- Install composer (needed to install dependencies).
- Go to the root directory for the extension.
- Run (install dependencies): composer --dev update
- Run (execute tests): phpunit