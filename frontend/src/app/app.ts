import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { Header } from './shared/layout/header';
import { Footer } from './shared/layout/footer';
import { ToastContainer } from './shared/toast/toast-container';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, Header, Footer, ToastContainer],
  templateUrl: './app.html',
  styleUrl: './app.scss'
})
export class App {}
