# What's New in Bag 2.7

## Memoization Support

Bag 2.7 introduces built-in support for [memoization](memoization), allowing you to cache 
instances of your value objects in memory. 

This can significantly improve performance when creating the same value objects multiple times, 
such as when working with related models in an Eloquent collection.
